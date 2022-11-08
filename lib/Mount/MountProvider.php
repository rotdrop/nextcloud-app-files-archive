<?php

namespace OCA\FilesArchive\Mount;

use Exception;
use Throwable;

// F I X M E internal
use OC\Files\Mount\MountPoint;
use OC\Files\Mount\MoveableMount;

use Psr\Log\LoggerInterface;

use OCP\Files\Mount\IMountManager;
use OCP\Files\Config\IMountProvider;
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

use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Service\ArchiveService;
use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Exceptions;

/**
 * Mount an archive-file as virtual file-system into the user-storage.
 */
class MountProvider implements IMountProvider
{
  use \OCA\FilesArchive\Traits\LoggerTrait;

  /** @var string */
  private $appName;

  /** @var int */
  private static $recursionLevel = 0;

  /** @var ArchiveMountMapper */
  private $mountMapper;

  /** @var IAppContainer */
  private $appContainer;

  /** @var IRootFolder */
  private $rootFolder;

  /** @var IMountManager */
  private $mountManager;

  /** @var IConfig */
  private $cloudConfig;

  /** @var IL10N */
  private $l;

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    string $appName,
    IConfig $cloudConfig,
    LoggerInterface $logger,
    IL10N $l10n,
    IAppContainer $appContainer,
    IRootFolder $rootFolder,
    IMountManager $mountManager,
    ArchiveMountMapper $mountMapper,
  ) {
    $this->appName = $appName;
    $this->cloudConfig = $cloudConfig;
    $this->logger = $logger;
    $this->l = $l10n;
    $this->appContainer = $appContainer;
    $this->rootFolder = $rootFolder;
    $this->mountManager = $mountManager;
    $this->mountMapper = $mountMapper;
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

  /** {@inheritdoc} */
  private function getMountsForUserInternal(IUser $user, IStorageFactory $loader)
  {
    $userId = $user->getUID();

    $userFolder = $this->rootFolder->getUserFolder($userId);
    if (empty($userFolder)) {
      return [];
    }

    $mounts = [];
    $mountMapping = $this->mountMapper->findAll($userId);

    $userFolderPath = $userFolder->getPath();

    /** @var ArchiveMount $mount */
    foreach ($mountMapping as $mount) {

      $archivePath = $mount->getArchiveFilePath();
      try {
        $archiveFile = $userFolder->get($archivePath);
      } catch (FileNotFoundException $e) {
        continue;
      }

      $archiveBombLimit = $this->cloudConfig->getAppValue(
        $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, Constants::DEFAULT_ADMIN_ARCHIVE_SIZE_LIMIT);
      $archiveSizeLimit = $this->cloudConfig->getUserValue(
        $userId, $this->appName, SettingsController::ARCHIVE_SIZE_LIMIT, null);
      $archiveSizeLimit = min($archiveBombLimit, $archiveSizeLimit ?? PHP_INT_MAX);

      // The mount-path must be absolute
      $mountDirectory = $userFolderPath . Constants::PATH_SEPARATOR . $mount->getMountPointPath();

      try {
        $storage = new ArchiveStorage([
          ArchiveStorage::PARAMETER_ARCHIVE_FILE => $archiveFile,
          ArchiveStorage::PARAMETER_APP_CONTAINER => $this->appContainer,
          ArchiveStorage::PARAMETER_ARCHIVE_SIZE_LIMIT => $archiveSizeLimit,
        ]);
      } catch (Exceptions\ArchiveException $e) {
        $this->logException($e, 'Skipping archive mount of "' . $archivePath . '".');
        continue;
      }

      $mounts[] = new class(
        $storage,
        $userFolderPath,
        $mountDirectory,
        $loader,
        $this->mountManager,
        $this->mountMapper,
        $mount,
        $this->logger,
      ) extends MountPoint implements MoveableMount
      {
        use \OCA\FilesArchive\Traits\LoggerTrait;

        /** @var IMountManager */
        private $mountManager;

        /** @var ArchiveMountMapper */
        private $mountMapper;

        /** @var ArchiveMount */
        private $mountEntity;

        /** @var string */
        private $userFolderPath;

        /**
         * @param ArchiveStorage $storage
         *
         * @param string $userFolderPath
         *
         * @param string $mountPointPath
         *
         * @param IStorageFactory $loader
         *
         * @param IMountManager $mountManager
         *
         * @param ArchiveMountMapper $mountMapper
         *
         * @param ArchiveMount $mountEntity
         *
         * @param LoggerInterface $logger
         */
        public function __construct(
          ArchiveStorage $storage,
          string $userFolderPath,
          string $mountPointPath,
          IStorageFactory $loader,
          IMountManager $mountManager,
          ArchiveMountMapper $mountMapper,
          ArchiveMount $mountEntity,
          LoggerInterface $logger,
        ) {
          parent::__construct(
            storage: $storage,
            mountpoint: $mountPointPath,
            loader: $loader,
            mountOptions: [
              'filesystem_check_changes' => 1,
              'readonly' => true,
              'previews' => true,
              'enable_sharing' => false, // cannot work, mount needs DB access
              'authenticated' => true,
            ]
          );
          $this->userFolderPath = $userFolderPath;
          $this->mountManager = $mountManager;
          $this->mountMapper = $mountMapper;
          $this->mountEntity = $mountEntity;
          $this->logger = $logger;
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

          $this->mountEntity->setMountPointPath($relativeTarget);
          $this->mountEntity->setMountPointPathHash(md5($relativeTarget));
          $this->mountMapper->update($this->mountEntity);

          return true;
        }

        /** {@inheritdoc} */
        public function removeMount()
        {
          $this->mountMapper->delete($this->mountEntity);
          $this->mountManager->removeMount($this->getMountPoint()); // neccessary?
          return true;
        }
      };
    }

    return $mounts;
  }
}
