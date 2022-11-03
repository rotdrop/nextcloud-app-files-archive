<?php

namespace OCA\FilesArchive\Mount;

use Exception;
use Throwable;

// F I X M E internal
use OC\Files\Mount\MountPoint;

use Psr\Log\LoggerInterface;

use OCP\Files\Config\IMountProvider;
use OCP\Files\Storage\IStorageFactory;
use OCP\Files\FileInfo;
use OCP\Files\NotFoundException as FileNotFoundException;
use OCP\Files\IRootFolder;
use OCP\IUser;
use OCP\IL10N;
use OCP\AppFramework\IAppContainer;

use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Service\ArchiveService;
use OCA\FilesArchive\Constants;

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

  /** @var IL10N */
  private $l;

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    string $appName,
    LoggerInterface $logger,
    IL10N $l10n,
    IAppContainer $appContainer,
    IRootFolder $rootFolder,
    ArchiveMountMapper $mountMapper,
  ) {
    $this->appName = $appName;
    $this->logger = $logger;
    $this->l = $l10n;
    $this->appContainer = $appContainer;
    $this->rootFolder = $rootFolder;
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

    /** @var ArchiveMount $mount */
    foreach ($mountMapping as $mount) {

      $archivePath = $mount->getArchiveFilePath();
      try {
        $archiveFile = $userFolder->get($archivePath);
      } catch (FileNotFoundException $e) {
        continue;
      }

      $mountDirectory = $mount->getMountPointPath();
      try {
        $mountFolder = $userFolder->get($mountDirectory);
      } catch (FileNotFoundException $e) {
        try {
          $mountFolder = $userFolder->newFolder($mountDirectory);
        } catch (Throwable $t) {
          //
        }
      }
      if (empty($mountFolder)) {
        continue;
      }

      $mountPath = $mountFolder->getPath();

      $storage = new ArchiveStorage([
        'appContainer' => $this->appContainer,
        'archiveFile' => $archiveFile,
      ]);

      $mounts[] = new class(
        $storage,
        $mountPath,
        null,
        $loader,
        [
          'filesystem_check_changes' => 1,
          'readonly' => true,
          'previews' => true,
          'enable_sharing' => false, // cannot work, mount needs DB access
          'authenticated' => true,
        ]
      ) extends MountPoint
      {
        /** {@inheritdoc} */
        public function getMountType()
        {
          return 'external'; // Constants::APP_NAME;
        }
      };
    }

    return $mounts;
  }
}
