<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @author    Fabio Fantoni <fabio.fantoni@m2r.biz>
 * @copyright 2022-2025 Claus-Justus Heine
 * @copyright 2026 Fabio Fantoni
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

// F I X M E internal
use Throwable;

use OC\Files\Mount\MountPoint;

use Psr\Log\LoggerInterface;

use OCP\Files\Mount\IMountManager;
use OCP\Files\Mount\IMovableMount;
use OCP\Files\Config\IUserMountCache;
use OCP\Files\Storage\IStorageFactory;

use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Service\NotificationService;
use OCA\FilesArchive\Storage\ArchiveStorage;

/**
 * Movable mount point wrapping an archive-file storage.
 */
class ArchiveMountPoint extends MountPoint implements IMovableMount
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  /**
   * @var bool
   *
   * Whether the persisted mount-point file-id has already been refreshed.
   */
  private bool $rootIdSynchronized = false;

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

  /**
   * {@inheritdoc}
   *
   * Keep the persisted file-id of the mount-point in sync with the file-cache.
   * The id is bound to the storage-id, which in turn depends on the path of
   * the archive file, so it goes stale as soon as the archive is renamed or
   * moved. The cache is rebuilt by the scanner in that case, and this is the
   * place where the resulting id becomes known again.
   */
  public function getStorageRootId()
  {
    $rootId = parent::getStorageRootId();

    if ($rootId > 0
        && !$this->rootIdSynchronized
        && $this->mountEntity->getId() !== null
        && $rootId !== $this->mountEntity->getMountPointFileId()) {
      $this->rootIdSynchronized = true;
      try {
        $this->mountEntity->setMountPointFileId($rootId);
        $this->mountMapper->update($this->mountEntity);
      } catch (Throwable $t) {
        $this->logger->error('Unable to update the mount-point file-id of the archive "' . $this->mountEntity->getArchiveFilePath() . '"', [ 'exception' => $t ]);
      }
    }

    return $rootId;
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
}
