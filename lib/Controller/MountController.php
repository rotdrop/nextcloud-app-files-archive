<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023, 2024 Claus-Justus Heine <himself@claus-justus-heine.de>
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

use Throwable;

use OC\Files\Storage\Wrapper\Wrapper as WrapperStorage;

use Psr\Log\LoggerInterface;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\DataResponse;
use OCP\IPreview;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IUserSession;
use OCP\Files\Mount\IMountPoint;
use OCP\Files\Mount\IMountManager;
use OCP\Files\IRootFolder;
use OCP\Files\FileInfo;
use OCP\Files\Node;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\NotFoundException as FileNotFoundException;

use OCA\FilesArchive\Toolkit\Exceptions as ToolkitExceptions;

use OCA\FilesArchive\Toolkit\Service\ArchiveService;
use OCA\FilesArchive\Service\ArchiveServiceFactory;
use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Mount\MountProvider;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Constants;

/**
 * Manage user mount requests for archive files.
 */
class MountController extends Controller
{
  use \OCA\FilesArchive\Toolkit\Traits\UtilTrait;
  use \OCA\FilesArchive\Toolkit\Traits\ResponseTrait;
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;
  use \OCA\FilesArchive\Toolkit\Traits\NodeTrait;
  use \OCA\FilesArchive\Toolkit\Traits\UserRootFolderTrait;
  use TargetPathTrait;
  use ArchiveSizeLimitTrait;

  /** @var string */
  private string $mountPointTemplate;

  /** @var bool */
  private bool $autoRenameMountPoint = false;

  /** @var bool */
  private bool $stripCommonPathPrefixDefault = false;

  /** @var null|int */
  private ?int $archiveSizeLimit = null;

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
  ) {
    parent::__construct($appName, $request);

    $user = $userSession->getUser();
    if (!empty($user)) {
      $this->userId = $user->getUID();

      $this->archiveBombLimit = $cloudConfig->getAppValue(
        $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT);
      $this->archiveSizeLimit = $cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, null);

      $this->mountPointTemplate = $cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::MOUNT_POINT_TEMPLATE, SettingsController::FOLDER_TEMPLATE_DEFAULT);

      $this->autoRenameMountPoint = (bool)$cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::MOUNT_POINT_AUTO_RENAME, false);

      $this->stripCommonPathPrefixDefault = (bool)$cloudConfig->getUserValue(
        $this->userId, $this->appName, SettingsController::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT, false);
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
   *
   * @NoAdminRequired
   */
  public function mount(
    string $archivePath,
    ?string $mountPointPath = null,
    ?string $passPhrase = null,
    ?bool $stripCommonPathPrefix = null,
  ) {
    $archivePath = urldecode($archivePath);
    if ($mountPointPath) {
      $mountPointPath = urldecode($mountPointPath);
    }

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    if (empty($userFolder)) {
      return self::grumble($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }

    $mounts = $this->mountMapper->findByArchivePath($this->userId, $archivePath);
    if (!empty($mounts)) {
      $mount = array_shift($mounts);
      return self::grumble($this->l->t('"%1$s" is already mounted on "%2$s".', [
        $archivePath, $mount->getMountPointPath(),
      ]));
    }

    try {
      /** @var File $archiveFile */
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      return self::grumble($this->l->t('Unable to open the archive file "%s".', $archivePath));
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
        return self::grumble($this->l->t('The archive file "%1$s" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveBombLimit)
        ]));
      } else {
        return self::grumble($this->l->t('The archive file "%1$s" is too large: uncompressed size %2$s > user limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveSizeLimit)
        ]));
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
      return self::grumnle($this->l->t(
        'Unable to open parent folder "%1$s" of mount point "%2$s": %3$s.', [
          $mountPointDirName, $mountPointBaseName, $t->getMessage()
        ]));
    }

    $nonExistingMountTarget = $parentFolder->getNonExistingName($mountPointBaseName);
    if ($nonExistingMountTarget != $mountPointBaseName) {
      if (!$this->autoRenameMountPoint) {
        return self::grumble($this->l->t('The mount point "%s" already exists and auto-rename is not enabled.', $mountPointPath));
      }
      $mountPointPath = $mountPointDirName . Constants::PATH_SEPARATOR . $nonExistingMountTarget;
    }

    // ok, just insert into our mounts table
    $mountEntity = new ArchiveMount;
    $mountEntity->setUserId($this->userId);
    $mountEntity->setMountPointPath($mountPointPath);
    $mountEntity->setArchiveFileId($archiveFile->getId());
    $mountEntity->setArchiveFilePath($archivePath);
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
    } catch (Throwable $t) {
      $this->logException($t);
      try {
        $this->mountManager->removeMount($mountPoint->MountPoint());
      } catch (Throwable $t) {
        // ignore
      }
      return self::grumble($this->l->t(
        'Unable to update the file cache for the mount point "%1s": %2$s.', [
          $mountPointPath, $t->getMessage()
        ]));
    }


    return self::dataResponse($this->formatMountEntity($mountEntity));
  }

  /**
   * @param string $archivePath
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function unmount(string $archivePath)
  {
    $archivePath = urldecode($archivePath);

    $mounts = $this->mountMapper->findByArchivePath($this->userId, $archivePath);
    if (empty($mounts)) {
      return self::grumble($this->l->t('"%s" is not mounted.', $archivePath));
    }

    $userFolder = $this->getUserFolder();
    if (empty($userFolder)) {
      return self::grumble($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
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

    return self::dataResponse([
      'errorMessages' => $errorMessages,
      'messages' => $messages,
      'count' => $unMountCount,
      'mounts' => $removedMountPoints,
    ], count($errorMessages) > 0 ? Http::STATUS_BAD_REQUEST : Http::STATUS_OK);
  }

  /**
   * Convert the given mount-point entity to a flat array and also add
   * information about the file-system node of the mount-point in order to be
   * able to communicate with the files-app files-listings.
   *
   * @param ArchiveMount $mount
   *
   * @return array
   */
  private function formatMountEntity(ArchiveMount $mount):array
  {
    $data = $mount->jsonSerialize();
    $userFolder = $this->getUserFolder();
    try {
      /** @var Folder $mountNode */
      $mountNode = $userFolder->get($mount->getMountPointPath());
      $data['mountPoint'] = $this->formatNode($mountNode);
    } catch (FileNotFoundException $notFound) {
      $this->logException($notFound);
      $data['mountPoint'] = false;
    }
    return $data;
  }

  /**
   * @param string $archivePath
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function mountStatus(string $archivePath):DataResponse
  {
    $archivePath = urldecode($archivePath);
    $mounts = $this->mountMapper->findByArchivePath($this->userId, $archivePath);
    return self::dataResponse([
      'messages' => [],
      'mounted' => !empty($mounts),
      'mounts' => array_map(fn(ArchiveMount $mount) => $this->formatMountEntity($mount), empty($mounts) ? [] : $mounts),
    ]);
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
   *
   * @NoAdminRequired
   */
  public function patch(string $archivePath, array $changeSet = [])
  {
    if (empty($changeSet)) {
      return self::dataResponse([
        'changeSet' => [],
      ]);
    }
    if (count($changeSet) != 1 || !array_key_exists('archivePassPhrase', $changeSet)) {
      return self::grumble($this->l->t('Only the passphrase may be changed for an existing mount.'));
    }
    $newPassPhrase = $changeSet['archivePassPhrase'];

    $archivePath = urldecode($archivePath);
    $mounts = $this->mountMapper->findByArchivePath($this->userId, $archivePath);

    $changeSet = [];

    /** @var ArchiveMount $mount */
    foreach ($mounts as $mount) {
      $mountPointPath = $mount->getMountPointPath();
      $changeSet[$mountPointPath]['old'] = $mount->getArchivePassPhrase();
      $changeSet[$mountPointPath]['new'] = $newPassPhrase;
      $mount->setArchivePassPhrase($newPassPhrase);
      $this->mountMapper->update($mount);
    }

    return self::dataResponse([
      'changeSet' => $changeSet,
    ]);
  }
}
