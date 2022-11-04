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
use OCP\AppFramework\IAppContainer;
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

use OCA\FilesArchive\Storage\ArchiveStorage;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Constants;

/**
 * Manage user mount requests for archive files.
 */
class MountController extends Controller
{
  use \OCA\FilesArchive\Traits\ResponseTrait;
  use \OCA\FilesArchive\Traits\LoggerTrait;
  use \OCA\FilesArchive\Traits\UtilTrait;

  /** @var string */
  private $userId;

  /** @var ArchiveMountMapper */
  private $mountMapper;

  /** @var IMountManager */
  private $mountManager;

  /** @var IRootFolder */
  private $rootFolder;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    ?string $appName,
    IRequest $request,
    ?string $userId,
    LoggerInterface $logger,
    IL10N $l10n,
    IConfig $config,
    IAppContainer $appContainer,
    IMountManager $mountManager,
    IRootFolder $rootFolder,
    ArchiveMountMapper $mountMapper,
  ) {
    parent::__construct($appName, $request);
    $this->logger = $logger;
    $this->l = $l10n;
    $this->config = $config;
    $this->userId = $userId;
    $this->appContainer = $appContainer;
    $this->mountMapper = $mountMapper;
    $this->mountManager = $mountManager;
    $this->rootFolder = $rootFolder;
  }
  // phpcs:enable

  /**
   * @param string $archivePath
   *
   * @param null|string $mountPoint
   *
   * @return DataResponse
   *
   * @NoAdminRequired
   */
  public function mount(string $archivePath, ?string $mountPoint = null)
  {
    $archivePath = urldecode($archivePath);
    if (empty($mountPoint)) {
      $pathInfo = pathinfo($archivePath);
      $mountPoint = $pathInfo['dirname'] . Constants::PATH_SEPARATOR . $pathInfo['filename'];
    } else {
      $mountPoint = urldecode($mountPoint);
    }
    $this->logInfo('ATTEMPT MOUNT POINT ' . $mountPoint);

    $userFolder = $this->rootFolder->getUserFolder($this->userId);
    if (empty($userFolder)) {
      return self::grumble($this->l->t('The user folder for user "%s" could not be opened.', $this->userId));
    }

    $mounts = $this->mountMapper->findByArchivePath($archivePath);
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
      /** @var Folder $mountPointFolder */
      $mountPointFolder = $userFolder->get($mountPoint);
      if ($mountPointFolder->getType() != FileInfo::TYPE_FOLDER) {
        return self::grumble($this->l->t('The mount point "%s" exists but is not a folder.', $mountPoint));
      }
    } catch (FileNotFoundException $e) {
      try {
        $mountPointFolder = $userFolder->newFolder($mountPoint);
      } catch (Throwable $t) {
        //
      }
    }

    if (empty($mountPointFolder)) {
      return self::grumble($this->l->t('The mount point "%s" does not exist and could not be created.', $mountPoint));
    }

    /** @var Folder $mountPointFolder */
    if (!empty($mountPointFolder->getDirectoryListing())) {
      return self::grumble($this->l->t('The mount point folder "%s" already contains files.', $mountPoint));
    }

    // ok, just insert in to our mounts table
    $mount = new ArchiveMount;
    $mount->setUserId($this->userId);
    $mount->setMountPointId($mountPointFolder->getId());
    $mount->setMountPointPath($mountPoint);
    $mount->setMountPointPathHash(md5($mountPoint));
    $mount->setArchiveFileId($archiveFile->getId());
    $mount->setArchiveFilePath($archivePath);
    $mount->setArchiveFilePathHash(md5($archivePath));
    $this->mountMapper->insert($mount);

    return self::dataResponse($mount->jsonSerialize());
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

    $mounts = $this->mountMapper->findByArchivePath($archivePath);
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

      try {
        /** @var Folder $mountPointFolder */
        $mountPointFolder = $userFolder->get($mountPointPath);
      } catch (Throwable $t) {
        $errorMessages[] = $this->l->t('Unable to open the top-level folder "%s" of the mounted archive.', $mountPointPath);
        continue;
      }

      $storage = $mountPointFolder->getStorage();
      // $storage = $mountPoint->getStorage();
      if (empty($storage)) {
        $errorMessages[] = $this->l->t('There is no storage behind the mount-point "%s".', $mountPointPath);
        continue;
      }
      while ($storage instanceof WrapperStorage) {
        $storage = $storage->getWrapperStorage();
      }
      if (!($storage instanceof ArchiveStorage)) {
        $errorMessages[] = $this->l->t('"%1$s" is a mount-point, but is not a mounted archive file, storage is "%2$s".', [
          $mountPointPath, $storage->getId(),
        ]);
        continue;
      }

      $this->mountManager->removeMount($mountPointPath);
      $mountPointFolder->delete();

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
    $mounts = $this->mountMapper->findByArchivePath($archivePath);
    return self::dataResponse([
      'mounted' => !empty($mounts),
      'mounts' => array_map(fn(ArchiveMount $mount) => $mount->jsonSerialize(), empty($mounts) ? [] : $mounts),
    ]);
  }
}
