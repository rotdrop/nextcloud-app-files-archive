<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2026 Claus-Justus Heine
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

namespace OCA\FilesArchive\Migration;

use Throwable;

use OCP\Files\IFile;
use OCP\Files\IRootFolder;
use OCP\Files\Mount\IMountManager;
use OCP\Files\Mount\IMountPoint;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use Psr\Log\LoggerInterface;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Mount\MountProvider;
use OCA\FilesArchive\Toolkit\Exceptions as ToolkitExceptions;

/**
 * Rebuild the file cache for all mounted archive files.
 */
class RebuildMountFileCache implements IRepairStep
{
  use \OCA\FilesArchive\Toolkit\Traits\LoggerTrait;

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected ArchiveMountMapper $mountMapper,
    protected IMountManager $mountManager,
    protected IRootFolder $rootFolder,
    protected LoggerInterface $logger,
    protected MountProvider $mountProvider,
    protected string $appName,
  ) {
  }
  // phpcs:enable

  /** {@inheritdoc} */
  public function getName()
  {
    return 'Rebuild the file cache for all mounted archive files';
  }

  /** {@inheritdoc} */
  public function run(IOutput $output)
  {
    $mounts = $this->mountMapper->findAll();
    $numberOfMounts = count($mounts);

    if ($numberOfMounts == 0) {
      return;
    }
    $output->startProgress($numberOfMounts);

    $previousUserId = null;
    $userFolder = null;

    /** @var ArchiveMount $mountEntity */
    for ($i = 0, $mountEntity = $mounts[$i]; $i < $numberOfMounts; $mountEntity = $mounts[++$i], $output->advance(1)) {
      $userId = $mountEntity->getUserId();
      $userFolderPrefix = Constants::PATH_SEP . $userId . Constants::PATH_SEP . 'files';
      $mountPointPath = $userFolderPrefix . $mountEntity->getMountPointPath();
      if ($previousUserId != $userId) {
        $previousUserId = $userId;
        try {
          $userFolder = $this->rootFolder->getUserFolder($userId);
        } catch (Throwable $t) {
          $userFolder = null;
          $output->info('Cannot access the home-folder of "' . $userId . '": ' . $t->getMessage());
          $output->info('Skipping mount "' . Constants::PATH_SEP . $userId . Constants::PATH_SEP . 'files' . $mountEntity->getMountPointPath() . '".');
          continue;
        }
      } elseif ($userFolder === null) {
        continue;
      }

      $archiveFiles = $userFolder->getById($mountEntity->getArchiveFileId());
      if (empty($archiveFiles)) {
        $output->info('Cannot access the referenced archive-file "' . $mountEntity->getArchiveFilePath() . '".');
        $output->info('Skipping mount "' . $mountPointPath . '".');
        continue;
      }

      /** @var IFile $archiveFile */
      foreach ($archiveFiles as $archiveFile) {
        $archiveFilePath = substr($archiveFile->getPath(), strlen($userFolderPrefix));
        if ($archiveFilePath == $mountEntity->getArchiveFilePath()) {
          break;
        }
        $archiveFile = null;
      }
      if ($archiveFile === null) {
        $archiveFile = array_shift($archiveFiles);
        $archiveFilePath = substr($archiveFile->getPath(), strlen($userFolderPrefix));
        $mountEntity->setArchiveFilePath($archiveFilePath);
        $this->mountMapper->update($mountEntity);
        $output->info('Archive file-path updated to "' . $archiveFilePath . '".');
      }

      try {
        /** @var IMountPoint $mountPoint */
        $mountPoint = $this->mountProvider->getMountPoint($mountEntity, $userId, PHP_INT_MAX);

        $this->mountManager->addMount($mountPoint);
        $storage = $mountPoint->getStorage();
        $storage->getScanner()->scan('');
        $storageRootId = $mountPoint->getStorageRootid();
        if ($storageRootId != $mountEntity->getMountPointFileId()) {
          $mountEntity->setMountPointFileId($mountPoint->getStorageRootId());
          $this->mountMapper->update($mountEntity);
        }
      } catch (Throwable $t) {
        $output->info('Unable to update the file-cache for "' . $mountPointPath . '": ' . $t->getMessage());
        $output->info('<warn>' . 'Skipping mount "' . $mountPointPath . '".');
      }
    }

    $output->finishProgress();
  }
}
