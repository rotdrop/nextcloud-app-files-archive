<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022-2025 Claus-Justus Heine
 * @license   AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\FilesArchive\Mount;

use Exception;
use Throwable;

// F I X M E internal
use OC\Files\Mount\MountPoint;
use OC\Files\Mount\MoveableMount;

use Psr\Log\LoggerInterface;

use OCP\Files\Mount\IMountManager;
use OCP\Files\Config\IMountProvider;
use OCP\Files\Config\IUserMountCache;
use OCP\Files\Storage\IStorageFactory;
use OCP\Files\FileInfo;
use OCP\Files\Folder;
use OCP\Files\NotFoundException as FileNotFoundException;
use OCP\Files\IRootFolder;
use OCP\IUser;
use OCP\IL10N;
use OCP\IConfig;
use OCP\AppFramework\IAppContainer;
use OCP\Lock\ILockingProvider as Locks;

use OCA\FilesArchive\Controller\SettingsController;
use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Service\ArchiveService;
use OCA\FilesArchive\Service\NotificationService;
use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Toolkit\Exceptions as ToolkitExceptions;

/**
 * Mount an archive file as virtual file system into the user-storage.
 */
class MountProvider implements IMountProvider
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  /** @var int */
  private static $recursionLevel = 0;

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    private ArchiveMountMapper $mountMapper,
    private IConfig $cloudConfig,
    private IMountManager $mountManager,
    private IRootFolder $rootFolder,
    private IUserMountCache $userMountCache,
    private NotificationService $notificationService,
    private string $appName,
    protected IAppContainer $appContainer,
    protected IL10N $l,
    protected LoggerInterface $logger,
  ) {
  }
  // phpcs:enable

  /** {@inheritdoc} */
  public function getMountsForUser(IUser $user, IStorageFactory $loader)
  {
    if (self::$recursionLevel > 0) {
      // the getNode() stuff below triggers recursion.
      // $this->logInfo('RECURSION: ' . self::$recursionLevel);
      return [];
    }
    self::$recursionLevel++;

    try {
      $mounts = $this->getMountsForUserInternal($user, $loader);
    } catch (Throwable $t) {
      $this->logException($t, 'Unable to generate mounts');
      $mounts = [];
    }

    --self::$recursionLevel;
    return $mounts;
  }

  /**
   * Wrapped by the real-function into a try-catch block
   *
   * @param IUser $user
   *
   * @param IStorageFactory $loader
   *
   * @return array
   */
  private function getMountsForUserInternal(IUser $user, IStorageFactory $loader)
  {
    $userId = $user->getUID();

    $userFolder = $this->rootFolder->getUserFolder($userId);
    if (empty($userFolder)) {
      return [];
    }

    $archiveBombLimit = $this->cloudConfig->getAppValue(
      $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT);
    $archiveSizeLimit = $this->cloudConfig->getUserValue(
      $userId, $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, null);
    $archiveSizeLimit = min($archiveBombLimit, $archiveSizeLimit ?? PHP_INT_MAX);

    $mounts = [];
    $mountMapping = $this->mountMapper->findAll($userId);

    /** @var ArchiveMount $mount */
    foreach ($mountMapping as $mountEntity) {

      $mountPoint = $this->doGetMountPoint(
        $mountEntity, $userId, $loader, $userFolder, $archiveSizeLimit,
      );
      if ($mountPoint === null) {
        continue;
      }

      $mounts[] = $mountPoint;
    }

    return $mounts;
  }

  /**
   * Convert the registered mount point info into a Nextcloud mount point.
   *
   * @param ArchiveMount $mountEntity
   *
   * @param string $userId
   *
   * @param int $archiveSizeLimit
   *
   * @return null|MoveableMount
   */
  public function getMountPoint(
    ArchiveMount $mountEntity,
    string $userId,
    int $archiveSizeLimit = Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT,
  ):?MoveableMount {

    $storageFactory = $this->appContainer->get(IStorageFactory::class);
    $userFolder = $this->rootFolder->getUserFolder($userId);
    if (empty($userFolder)) {
      return null;
    }

    return $this->doGetMountPoint(
      $mountEntity, $userId, $storageFactory, $userFolder, $archiveSizeLimit,
    );
  }

  /**
   * Convert the registered mount point info into a Nextcloud mount point.
   *
   * @param ArchiveMount $mountEntity
   *
   * @param string $userId
   *
   * @param IStorageFactory $loader
   *
   * @param Folder $userFolder
   *
   * @param int $archiveSizeLimit
   *
   * @return null|MoveableMount
   */
  private function doGetMountPoint(
    ArchiveMount $mountEntity,
    string $userId,
    IStorageFactory $loader,
    Folder $userFolder,
    int $archiveSizeLimit = Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT,
  ):?MoveableMount {

    $userFolderPath = $userFolder->getPath();

    $archivePath = $mountEntity->getArchiveFilePath();
    try {
      $archiveFile = $userFolder->get($archivePath);
    } catch (FileNotFoundException $e) {
      return null;
    }
    $passPhrase = $mountEntity->getArchivePassPhrase();

    // The mount-path must be absolute
    $mountDirectory = $userFolderPath . Constants::PATH_SEPARATOR . $mountEntity->getMountPointPath();

    // Whether to strip a common path prefix
    $stripCommonPathPrefix = !!($mountEntity->getMountFlags() & ArchiveMount::MOUNT_FLAG_STRIP_COMMON_PATH_PREFIX);

    try {
      $storage = new ArchiveStorage([
        ArchiveStorage::PARAMETER_ARCHIVE_FILE => $archiveFile,
        ArchiveStorage::PARAMETER_ARCHIVE_PASS_PHRASE => $passPhrase,
        ArchiveStorage::PARAMETER_APP_CONTAINER => $this->appContainer,
        ArchiveStorage::PARAMETER_ARCHIVE_SIZE_LIMIT => $archiveSizeLimit,
        ArchiveStorage::PARAMETER_STRIP_COMMON_PATH_PREFIX => $stripCommonPathPrefix,
      ]);
    } catch (ToolkitExceptions\ArchiveException $e) {
      $this->logException($e, 'Skipping archive mount of "' . $archivePath . '".');
      return null;
    }

    if ($mountEntity->getMountPointFileId()) {
      $storage->setRootId($mountEntity->getMountPointFileId());
    }

    return new class(
      $storage,
      $mountDirectory,
      $loader,
      $userFolderPath,
      $this->mountManager,
      $this->userMountCache,
      $this->mountMapper,
      $mountEntity,
      $this->notificationService,
      $this->logger,
    ) extends MountPoint implements MoveableMount
    {
      use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

      /**
       * @param ArchiveStorage $storage
       *
       * @param string $mountPointPath
       *
       * @param IStorageFactory $loader
       *
       * @param string $userFolderPath
       *
       * @param IMountManager $mountManager
       *
       * @param IUserMountCache $userMountCache
       *
       * @param ArchiveMountMapper $mountMapper
       *
       * @param ArchiveMount $mountEntity
       *
       * @param NotificationService $notificationService
       *
       * @param LoggerInterface $logger
       */
      public function __construct(
        ArchiveStorage $storage,
        string $mountPointPath,
        IStorageFactory $loader,
        private string $userFolderPath,
        private IMountManager $mountManager,
        private IUserMountCache $userMountCache,
        private ArchiveMountMapper $mountMapper,
        private ArchiveMount $mountEntity,
        private NotificationService $notificationService,
        protected LoggerInterface $logger,
      ) {
        parent::__construct(
          storage: $storage,
          mountpoint: $mountPointPath,
          loader: $loader,
          mountId: $mountEntity->getArchiveFileId(),
          mountProvider: MountProvider::class,
          mountOptions: [
            'filesystem_check_changes' => 1,
            'readonly' => true,
            'previews' => true,
            'enable_sharing' => false,
            'authenticated' => false,
          ],
        );
      }

      /** {@inheritdoc} */
      public function getMountType()
      {
        return 'external'; // Constants::APP_NAME;
      }

      /** {@inheritdoc} */
      public function moveMount($target)
      {
        if (!str_starts_with($target, $this->userFolderPath)) {
          return false;
        }

        $relativeTarget = substr($target, strlen($this->userFolderPath));

        // has to be done, the file-cache is then updated automagically, it seems
        $this->setMountPoint($target);

        $this->mountEntity->setMountPointPath($relativeTarget);
        $this->mountMapper->update($this->mountEntity);

        // Convenience: obviously the user has realized the mount, so there is
        // no point in keeping the notification.
        $this->notificationService->deleteMountSuccessNotification(
          userId: $this->mountEntity->getUserId(),
          mountPointId: $this->mountEntity->getMountPointFileId(),
        );

        return true;
      }

      /** {@inheritdoc} */
      public function removeMount()
      {
        /** @var MountPoint $this */
        $this->mountMapper->delete($this->mountEntity);
        $this->mountManager->removeMount($this->getMountPoint());

        $this->userMountCache->remoteStorageMounts($this->getNumericStorageId());

        // Destroy the cache s.t. we will get a new id on the next mount. This
        // is necessary to get the display in the front-end correct because
        // the path cache (Vue "store") does not listen to "deleted" events.
        /** @var ArchiveStorage $storage */
        $storage = $this->getStorage();
        // $storage->getCache()->remove($this->getStorageRootId());
        // $storage->getStorageCache()->remove($this->getNumericStorageId());
        $storage->getCache()->clear(); // internal, but much faster

        // Remove the mount-success information in order not to have stale
        // notifications with links to non-existing files.
        $this->notificationService->deleteMountSuccessNotification(
          userId: $this->mountEntity->getUserId(),
          mountPointId: $this->mountEntity->getMountPointFileId(),
        );

        return true;
      }
    };
  }
}
