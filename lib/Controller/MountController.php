<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
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
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Files\Mount\IMountPoint;
use OCP\Files\Mount\IMountManager;
use OCP\Files\IRootFolder;
use OCP\Files\FileInfo;
use OCP\Files\Node;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\NotFoundException as FileNotFoundException;

use OCA\RotDrop\Toolkit\Service\ArchiveService;
use OCA\RotDrop\Toolkit\Exceptions as ToolkitExceptions;

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
  use \OCA\RotDrop\Toolkit\Traits\ResponseTrait;
  use \OCA\RotDrop\Toolkit\Traits\LoggerTrait;
  use \OCA\RotDrop\Toolkit\Traits\UtilTrait;
  use TargetPathTrait;

  /** @var string */
  private $userId;

  /** @var ArchiveMountMapper */
  private $mountMapper;

  /** @var IMountManager */
  private $mountManager;

  /** @var IRootFolder */
  private $rootFolder;

  /** @var ArchiveService */
  private $archiveService;

  /** @var MountProvider */
  private $mountProvider;

  /** @var string */
  private $mountPointTemplate;

  /** @var bool */
  private $autoRenameMountPoint = false;

  /** @var bool */
  private $stripCommonPathPrefixDefault = false;

  /** @var null|int */
  private $archiveSizeLimit = null;

  /** @var int */
  private $archiveBombLimit = Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT;


  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    ?string $userId,
    LoggerInterface $logger,
    IL10N $l10n,
    IConfig $cloudConfig,
    IMountManager $mountManager,
    IRootFolder $rootFolder,
    ArchiveMountMapper $mountMapper,
    ArchiveService $archiveService,
    MountProvider $mountProvider,
  ) {
    parent::__construct($appName, $request);
    $this->logger = $logger;
    $this->l = $l10n;
    $this->userId = $userId;
    $this->mountMapper = $mountMapper;
    $this->mountManager = $mountManager;
    $this->rootFolder = $rootFolder;
    $this->mountProvider = $mountProvider;
    $this->archiveService = $archiveService;
    $this->archiveService->setL10N($l10n);

    $this->archiveBombLimit = $cloudConfig->getAppValue(
      $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT);
    $this->archiveSizeLimit = $cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, null);

    $this->archiveService->setSizeLimit(min($this->archiveBombLimit, $this->archiveSizeLimit ?? PHP_INT_MAX));

    $this->mountPointTemplate = $cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::MOUNT_POINT_TEMPLATE, SettingsController::FOLDER_TEMPLATE_DEFAULT);

    $this->autoRenameMountPoint = (bool)$cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::MOUNT_POINT_AUTO_RENAME, false);

    $this->stripCommonPathPrefixDefault = (bool)$cloudConfig->getUserValue(
      $this->userId, $this->appName, SettingsController::MOUNT_STRIP_COMMON_PATH_PREFIX_DEFAULT, false);
  }
  // phpcs:enable

  /**
   * @param string $archivePath
   *
   * @param null|string $mountPoint
   *
   * @param null|string $passPhrase
   *
   * @param null|bool $stripCommonPathPrefix
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function mount(string $archivePath, ?string $mountPointPath = null, ?string $passPhrase = null, ?bool $stripCommonPathPrefix = null)
  {
    $archivePath = urldecode($archivePath);

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

    try {
      $this->archiveService->open($archiveFile);
    } catch (ToolkitExceptions\ArchiveTooLargeException $e) {
      $uncompressedSize = $e->getActualSize();
      // $archiveInfo = $e->getArchiveInfo();
      if ($uncompressedSize > $this->archiveBombLimit) {
        return self::grumble($this->l->t('The archive-file "%1$s" appears to be a zip-bomb: uncompressed size %2$s > admin limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveBombLimit)
        ]));
      } else {
        return self::grumble($this->l->t('The archive-file "%1$s" is too large: uncompressed size %2$s > user limit %3$s.', [
          $archivePath, $this->formatStorageValue($uncompressedSize), $this->formatStorageValue($this->archiveSizeLimit)
        ]));
      }
    }

    if (empty($mountPointPath)) {
      $mountPointDirName = dirname($archivePath);
      $mountPointBaseName = $this->defaultMountPointName();
      $mountPointPath = $mountPointDirName . Constants::PATH_SEPARATOR . $mountPointBaseName;
    } else {
      $mountPointPath = urldecode($mountPointPath);
      $mountPointBaseName = basename($mountPointPath);
      $mountPointDirName = dirname($mountPointPath);
    }

    // avoid "over-mounting" existing directories
    try {
      /** @var Folder $parentFolder */
      $parentFolder = $userFolder->get($mountPointDirName);
    } catch (Throwable $t) {
      $this->logException($t);
      return self::grumnle($this->l->t(
        'Unable to open parent-folder "%1$s" of moint-point "%2$s": %3$s.', [
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

    $mountFlags = 0;
    if ($stripCommonPathPrefix ?? $this->stripCommonPathPrefixDefault) {
      $mountFlags |= ArchiveMount::MOUNT_FLAG_STRIP_COMMON_PATH_PREFIX;
      $this->logInfo('SHOULD STRIP PATH');
    }

    // ok, just insert in to our mounts table
    $mountEntity = new ArchiveMount;
    $mountEntity->setUserId($this->userId);
    $mountEntity->setMountPointPath($mountPointPath);
    $mountEntity->setMountPointPathHash(md5($mountPointPath));
    $mountEntity->setArchiveFileId($archiveFile->getId());
    $mountEntity->setArchiveFilePath($archivePath);
    $mountEntity->setArchiveFilePathHash(md5($archivePath));
    $mountEntity->setArchivePassPhrase($passPhrase);
    $mountEntity->setMountFlags($mountFlags);
    $this->mountMapper->insert($mountEntity);

    try {
      // obtain the mount-point and run the scanner
      /** @var IMountPoint $mountPoint */
      $mountPoint = $this->mountProvider->getMountPoint($mountEntity, $this->userId, $this->archiveService->getSizeLimit());

      $this->mountManager->addMount($mountPoint);
      $storage = $mountPoint->getStorage();
      $storage->getScanner()->scan('');
    } catch (Throwable $t) {
      $this->logException($t);
      $this->mountMapper->delete($mountEntity);
      return self::grumble($this->l->t(
        'Unable to update the file cache for the mount-point "%1s": %2$s.', [
          $mountPointPath, $t->getMessage()
        ]));
    }

    return self::dataResponse($mountEntity->jsonSerialize());
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

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    if (empty($userFolder)) {
      return self::grumble($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }

    $unMountCount = 0;
    $messages = [];
    $errorMessages = [];
    foreach ($mounts as $mount) {
      $mountPointPath = $mount->getMountPointPath();

      /** @var IMountPoint $mountPoint */
      $mountPoint = $this->mountManager->find($mountPointPath);
      if (empty($mountPoint)) {
        $errorMessages[] = $this->l->t('Directory "%s" should be a mount point, but it is not.', $mountPointPath);
        continue;
      }

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
    ], count($errorMessages) > 0 ? Http::STATUS_BAD_REQUEST : Http::STATUS_OK);
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
      'mounts' => array_map(fn(ArchiveMount $mount) => $mount->jsonSerialize(), empty($mounts) ? [] : $mounts),
    ]);
  }

  /**
   * This method is primarily (and ATM only) for patching the archive file
   * password into existing mounts. It seems that some archive formats (zip
   * e.g.) allow listing of the archive and only start to complain about a
   * missing pass-phrase when trying to extract data.
   *
   * The idea here is that the user can add a missing password after a mount
   * seems to have succeeded as the archive listing is there, but files cannot
   * be extracted as the password is missing.
   *
   * @param string $archivePath
   *
   * @param array $changeSet Properties to be patched into the existing
   * mount. ATM only the pass-phrase may be changed.
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
      return self::grumble($this->l->t('Only the pass-phrase may be changed for an existing mount.'));
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
