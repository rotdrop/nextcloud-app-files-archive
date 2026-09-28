<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023, 2024, 2025, 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *"
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\FilesArchive\Controller;

use SensitiveParameter;
use Throwable;

use OC\Files\Storage\Wrapper\Wrapper as WrapperStorage;

use Psr\Log\LoggerInterface;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute as CoreAttributes;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IPreview;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IUser;
use OCP\IUserSession;
use OCP\Files\Mount\IMountPoint;
use OCP\Files\Mount\IMountManager;
use OCP\Files\IRootFolder;
use OCP\Files\FileInfo;
use OCP\Files\Node;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\NotFoundException as FileNotFoundException;
use OCP\Files\Events\InvalidateMountCacheEvent;

use OCA\FilesArchive\Toolkit\Exceptions as ToolkitExceptions;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Controller\DTO;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Mount\MountProvider;
use OCA\FilesArchive\Service\ArchiveServiceFactory;
use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Toolkit\Exceptions\EnduserNotificationException;
use OCA\FilesArchive\Toolkit\Service\ArchiveService;

/**
 * Manage user mount requests for archive files.
 */
class MountController extends Controller
{
  use ArchiveSizeLimitTrait;
  use TargetPathTrait;
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Toolkit\Traits\NodeTrait;
  use \OCA\FilesArchive\Toolkit\Traits\ResponseTrait;
  use \OCA\FilesArchive\Toolkit\Traits\UserRootFolderTrait;
  use \OCA\FilesArchive\Toolkit\Traits\UtilTrait;
  use \OCA\FilesArchive\Traits\GetArchiveFileTrait;

  /** @var string */
  private string $mountPointTemplate;

  /** @var bool */
  private bool $autoRenameMountPoint = false;

  /** @var bool */
  private bool $stripCommonPathPrefixDefault = false;

  /** @var bool */
  private bool $mountDisabled = false;

  /** @var null|int */
  private ?int $archiveSizeLimit = null;

  /** @var null|IUser */
  private ?IUser $user = null;

  /** @var int */
  private int $archiveBombLimit = Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    protected LoggerInterface $logger,
    protected IL10N $l,
    private IMountManager $mountManager,
    protected IRootFolder $rootFolder,
    private ArchiveMountMapper $mountMapper,
    private ArchiveServiceFactory $archiveServiceFactory,
    private MountProvider $mountProvider,
    protected IPreview $previewManager,
    IConfig $cloudConfig,
    IUserSession $userSession,
    private IEventDispatcher $eventDispatcher,
  ) {
    parent::__construct($appName, $request);

    $user = $userSession->getUser();
    if (!empty($user)) {
      $this->user = $user;
      $this->userId = $user->getUID();

      $this->archiveBombLimit = $cloudConfig->getAppValue(
        $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT);
      $this->archiveSizeLimit = $cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, null);

      $this->mountPointTemplate = $cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::MOUNT_POINT_TEMPLATE, SettingsController::FOLDER_TEMPLATE_DEFAULT);

      $this->autoRenameMountPoint = (bool)$cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::MOUNT_POINT_AUTO_RENAME, false);

      $stripCommonPathPrefixDefault = (bool)$cloudConfig->getAppValue(
        $this->appName,
        SettingsController::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT,
        SettingsController::STRIP_COMMON_PATH_PREFIX_DEFAULT);

      $this->stripCommonPathPrefixDefault = (bool)$cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT,
        $stripCommonPathPrefixDefault);

      $mountDisabledDefault = (bool)$cloudConfig->getAppValue(
        $this->appName, SettingsController::MOUNT_DISABLED, SettingsController::MOUNT_DISABLED_DEFAULT);

      $this->mountDisabled = (bool)$cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::MOUNT_DISABLED, $mountDisabledDefault);
    }
  }
  // phpcs:enable

  /**
   * @param string $archivePath
   *
   * @param null|string $mountPointPath
   *
   * @param null|string $passPhrase
   *
   * @param null|bool $stripCommonPathPrefix
   *
   * @return DataResponse
   */
  #[CoreAttributes\NoAdminRequired]
  #[CoreAttributes\FrontpageRoute(
    verb: 'POST',
    url: '/archive/mount/{archivePath}/{mountPointPath}',
    defaults: [
      'mountPointPath' => null,
    ],
  )]
  public function mount(
    string $archivePath,
    ?string $mountPointPath = null,
    #[SensitiveParameter]
    ?string $passPhrase = null,
    ?bool $stripCommonPathPrefix = null,
  ): DataResponse|JSONResponse {
    if ($this->mountDisabled) {
      throw new EnduserNotificationException(
        $this->l->t('Mounting of archive files is disabled. You can enable it in your personal settings.'),
      );
    }

    $archivePath = urldecode($archivePath);
    if ($mountPointPath) {
      $mountPointPath = urldecode($mountPointPath);
    }

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    if (empty($userFolder)) {
      throw new EnduserNotificationException(
        $this->l->t('The user folder for user "%s" could not be opened.', $this->userId),
      );
    }

    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      throw new EnduserNotificationException(
        $this->l->t('Unable to open the archive file "%s".', $archivePath),
      );
    }
    $archiveFileId = $archiveFile->getId();

    $mounts = $this->mountMapper->findByArchiveFileId($this->userId, $archiveFileId);
    if (!empty($mounts)) {
      $mount = array_shift($mounts);
      throw new EnduserNotificationException(
        $this->l->t('"%1$s" is already mounted on "%2$s".', [
          $archivePath, $mount->getMountPointPath(),
        ]),
      );
    }

    $mountFlags = 0;
    if ($stripCommonPathPrefix ?? $this->stripCommonPathPrefixDefault) {
      $mountFlags |= ArchiveMount::MOUNT_FLAG_STRIP_COMMON_PATH_PREFIX;
    }

    /** @var ArchiveService $archiveService */
    try {
      $archiveService = $this->archiveServiceFactory->get($archiveFile);
      $archiveService->setSizeLimit($this->actualArchiveSizeLimit());
      $archiveService->open($archiveFile, password: $passPhrase);
    } catch (ToolkitExceptions\ArchiveTooLargeException $e) {
      $uncompressedSize = $e->getActualSize();
      if ($uncompressedSize > $this->archiveBombLimit) {
        throw new EnduserNotificationException(
          $this->l->t('The archive file "%1$s" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s.', [
            $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveBombLimit)
          ]),
        );
      } else {
        throw new EnduserNotificationException(
          $this->l->t('The archive file "%1$s" is too large: uncompressed size %2$s > user limit %3$s.', [
            $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveSizeLimit)
          ]),
        );
      }
    }

    list(
      'path' => $mountPointPath,
      'baseName' => $mountPointBaseName,
      'dirName' => $mountPointDirName,
    ) = $this->targetPathInfo($mountPointPath, $archivePath, 'mount');

    // avoid "over-mounting" existing directories
    try {
      /** @var Folder $parentFolder */
      $parentFolder = $userFolder->get($mountPointDirName);
    } catch (Throwable $t) {
      $this->logException($t);
      throw new EnduserNotificationException($this->l->t(
        'Unable to open parent folder "%1$s" of mount point "%2$s": %3$s.', [
          $mountPointDirName, $mountPointBaseName, $t->getMessage()
        ]));
    }

    $nonExistingMountTarget = $parentFolder->getNonExistingName($mountPointBaseName);
    if ($nonExistingMountTarget != $mountPointBaseName) {
      if (!$this->autoRenameMountPoint) {
        throw new EnduserNotificationException($this->l->t('The mount point "%s" already exists and auto-rename is not enabled.', $mountPointPath));
      }
      $mountPointPath = $mountPointDirName . Constants::PATH_SEPARATOR . $nonExistingMountTarget;
    }

    // ok, just insert into our mounts table
    $mountEntity = new ArchiveMount;
    $mountEntity->setUserId($this->userId);
    $mountEntity->setMountPointPath($mountPointPath);
    $mountEntity->setArchiveFileId($archiveFile->getId());
    $mountEntity->setArchivePassPhrase($passPhrase);
    $mountEntity->setMountFlags($mountFlags);

    try {
      // obtain the mount point and run the scanner
      /** @var IMountPoint $mountPoint */
      $mountPoint = $this->mountProvider->getMountPoint($mountEntity, $this->userId, $archiveService->getSizeLimit());

      $this->mountManager->addMount($mountPoint);
      $storage = $mountPoint->getStorage();
      // $this->logInfo('START THE SCANNER');
      $storage->getScanner()->scan('');
      // $this->logInfo('FINISHED SCANNING');

      // only now we have the root-id
      $mountEntity->setMountPointFileId($mountPoint->getStorageRootId());
      $this->mountMapper->insert($mountEntity);
      $this->invalidateMountCache();
    } catch (Throwable $t) {
      $this->logException($t);
      try {
        $this->mountManager->removeMount($mountPoint->MountPoint());
      } catch (Throwable $t) {
        // ignore
      }
      throw new EnduserNotificationException($this->l->t(
        'Unable to update the file cache for the mount point "%1s": %2$s.', [
          $mountPointPath, $t->getMessage()
        ]));
    }

    $result = $this->formatMountEntity($mountEntity, throwOnError: true);

    return new DTO\ArchiveMountResponse(mount: $result->mount, mountPoint: $result->mountPoint)->response();
  }

  /**
   * @param string $archivePath
   *
   * @return DataResponse
   */
  #[CoreAttributes\NoAdminRequired]
  #[CoreAttributes\FrontpageRoute(verb: 'POST', url: '/archive/unmount/{archivePath}')]
  public function unmount(string $archivePath): DataResponse|JSONResponse
  {
    $archivePath = urldecode($archivePath);

    $userFolder = $this->getUserFolder();
    if (empty($userFolder)) {
      throw new EnduserNotificationException($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }

    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      throw new EnduserNotificationException($this->l->t('Unable to open the archive file "%s".', $archivePath));
    }
    $archiveFileId = $archiveFile->getId();

    $mounts = $this->mountMapper->findByArchiveFileId($this->userId, $archiveFileId);
    if (empty($mounts)) {
      throw new EnduserNotificationException($this->l->t('"%s" is not mounted.', $archivePath));
    }

    $unMountCount = 0;
    $messages = [];
    $errorMessages = [];
    $removedMountPoints = [];
    foreach ($mounts as $mount) {
      $mountPointPath = $mount->getMountPointPath();

      /** @var IMountPoint $mountPoint */
      $mountPoint = $this->mountManager->find($mountPointPath);
      if (empty($mountPoint)) {
        $errorMessages[] = $this->l->t('Directory "%s" is not a mount point.', $mountPointPath);
        continue;
      }

      $removedMountPoints[] = $this->formatMountEntity($mount);

      $this->mountManager->removeMount($mountPointPath);
      $this->mountMapper->delete($mount);

      $messages[] = $this->l->t('Archive "%1$s" has been unmounted from "%2$s".', [
        $archivePath, $mountPointPath
      ]);

      ++$unMountCount;
    }
    if ($unMountCount > 0) {
      $this->invalidateMountCache();
    }

    return new DTO\ArchiveUnmountResponse(
      errorMessages: $errorMessages,
      messages: $messages,
      count: $unMountCount,
      mounts: $removedMountPoints,
    )->response(count($errorMessages) > 0 ? Http::STATUS_BAD_REQUEST : Http::STATUS_OK);
  }

  /**
   * Since NC 33 the file-system setup of later requests relies on the cached
   * mounts of the user, so a mount added or removed here would stay
   * invisible (resp. visible) until the next full setup.
   *
   * @return void
   */
  private function invalidateMountCache(): void
  {
    $this->eventDispatcher->dispatchTyped(new InvalidateMountCacheEvent($this->user));
  }

  /**
   * Convert the given mount-point entity to a flat array and also add
   * information about the file-system node of the mount-point in order to be
   * able to communicate with the files-app files-listings.
   *
   * @param ArchiveMount $mount
   *
   * @param bool $throwOnError
   *
   * @return DTO\ArchiveMountResponse
   */
  private function formatMountEntity(ArchiveMount $mount, bool $throwOnError = false): DTO\MountPoint
  {
    try {
      $userFolder = $this->getUserFolder();
      /** @var Folder $mountNode */
      $mountNode = $userFolder->get($mount->getMountPointPath());
      $mountPoint = $this->formatNode($mountNode);
    } catch (FileNotFoundException $notFound) {
      if ($throwOnError) {
        throw new EnduserNotificationException(
          $this->l->t('Unable to access the archive mount point at "%1$s".', $mount->getMountPointPath()),
          0,
          $notFound,
          context: $mount->jsonSerialize(),
        );
      }
      $this->logException($notFound);
      $mountPoint = null;
    }

    return new DTO\MountPoint(
      mount: $mount,
      mountPoint: $mountPoint,
    );
  }

  /**
   * @param string $archivePath
   *
   * @return DataResponse
   */
  #[CoreAttributes\NoAdminRequired]
  #[CoreAttributes\FrontpageRoute(verb: 'GET', url: '/archive/mount/{archivePath}')]
  public function mountStatus(string $archivePath): DataResponse|JSONResponse
  {
    $archivePath = urldecode($archivePath);

    $userFolder = $this->getUserFolder();
    if (empty($userFolder)) {
      throw new EnduserNotificationException($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }

    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      throw new EnduserNotificationException($this->l->t('Unable to open the archive file "%s".', $archivePath));
    }
    $archiveFileId = $archiveFile->getId();

    $mounts = $this->mountMapper->findByArchiveFileId($this->userId, $archiveFileId);

    $mountsWithMountPoint = array_filter(
      array_map(fn(ArchiveMount $mount) => $this->formatMountEntity($mount), empty($mounts) ? [] : $mounts),
      fn(DTO\MountPoint $mountPoint) => $mountPoint->mountPoint !== null,
    );

    return new DTO\MountStatusResponse(
      messages: [],
      mounted: !empty($mountsWithMountPoint),
      mounts: $mountsWithMountPoint,
    )->response(count($mountsWithMountPoint) === count($mounts) ? HTTP::STATUS_OK : HTTP::STATUS_INTERNAL_SERVER_ERROR);
  }

  /**
   * This method is primarily (and ATM only) for patching the archive file
   * password into existing mounts. It seems that some archive formats (zip
   * e.g.) allow listing of the archive and only start to complain about a
   * missing passphrase when trying to extract data.
   *
   * The idea here is that the user can add a missing password after a mount
   * seems to have succeeded as the archive listing is there, but files cannot
   * be extracted as the password is missing.
   *
   * @param string $archivePath
   *
   * @param array $changeSet Properties to be patched into the existing
   * mount. ATM only the passphrase may be changed.
   *
   * @return DataResponse
   */
  #[CoreAttributes\NoAdminRequired]
  #[CoreAttributes\FrontpageRoute(verb: 'PATCH', url: '/archive/mount/{archivePath}')]
  public function patch(string $archivePath, #[SensitiveParameter] array $changeSet = []): DataResponse|JSONResponse
  {
    if (empty($changeSet)) {
      return new DTO\MountPatchResponse(
        changeSet: [],
      )->response();
    }
    if (count($changeSet) != 1 || !array_key_exists('archivePassPhrase', $changeSet)) {
      throw new EnduserNotificationException($this->l->t('Only the passphrase may be changed for an existing mount.'));
    }
    $newPassPhrase = $changeSet['archivePassPhrase'];

    $archivePath = urldecode($archivePath);

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    if (empty($userFolder)) {
      throw new EnduserNotificationException($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }

    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      throw new EnduserNotificationException($this->l->t('Unable to open the archive file "%s".', $archivePath));
    }
    $archiveFileId = $archiveFile->getId();

    $mounts = $this->mountMapper->findByArchiveFileId($this->userId, $archiveFileId);

    $changeSet = [];

    /** @var ArchiveMount $mount */
    foreach ($mounts as $mount) {
      $mountPointPath = $mount->getMountPointPath();
      $changeSet[$mountPointPath]['old'] = $mount->getArchivePassPhrase();
      $changeSet[$mountPointPath]['new'] = $newPassPhrase;
      $mount->setArchivePassPhrase($newPassPhrase);
      $this->mountMapper->update($mount);
    }

    return new DTO\MountPatchResponse(
      changeSet: $changeSet,
    )->response();
  }
}
