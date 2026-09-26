<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2024, 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\Command;

use Throwable;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OCP\Files\File;
use OCP\Files\IRootFolder;
use OCP\Files\Mount\IMountManager;
use OCP\Files\Mount\IMountPoint;
use OCP\Files\NotFoundException;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;
use OCA\FilesArchive\Mount\MountProvider;
use OCA\FilesArchive\Service\ArchiveServiceFactory;
use OCA\FilesArchive\Toolkit\Exceptions as ToolkitExceptions;

/** Recreate the file-cache for mounted archives. */
class RebuildMountFileCache extends Command
{

  /** {@inheritdoc} */
  public function __construct(
    protected ArchiveMountMapper $mountMapper,
    protected ArchiveServiceFactory $archiveServiceFactory,
    protected IMountManager $mountManager,
    protected IRootFolder $rootFolder,
    protected MountProvider $mountProvider,
    protected string $appName,
  ) {
    parent::__construct();
  }

  /** {@inheritdoc} */
  protected function configure()
  {
    $this
      ->setName($this->appName . ':recreate-cache')
      ->setDescription('Recreate the cloud file-cache for mounted archives.')
      ->addOption(
        'user',
        'u',
        InputOption::VALUE_REQUIRED,
        'Restrict the operation to the given user-id.',
      )
      ->addOption(
        'all',
        'a',
        InputOption::VALUE_NONE,
        'Work on all mounted archives of all users or the given user.',
      )
      ->addOption(
        'mount-point',
        'm',
        InputOption::VALUE_REQUIRED,
        'Restrict the operation to the given mount-point.',
      );
  }

  /** {@inheritdoc} */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $userId = $input->getOption('user');
    $all = $input->getOption('all');
    $mountPoint = $input->getOption('mount-point');

    if (empty($all) && empty($mountPoint)) {
      $output->writeln('<error>' . 'One of the options "--mount-point=MOUNT_POINT" or "--all" has to be given.' . '</error>');
      $output->writeln('');
      (new DescriptorHelper)->describe($output, $this);
      return Command::INVALID;
    }
    if (!empty($mountPoint)) {
      $mountPoint = Constants::PATH_SEP . ltrim($mountPoint, Constants::PATH_SEP);
      if (empty($userId)) {
        $components = explode(Constants::PATH_SEP, $mountPoint);
        if (($components[2] ?? null) != 'files') {
          $output->writeln('<error>' . 'When specifying a mount but no user-id then the mount-point has to be of the form "/[USER_ID]/files/...".' . '</error>');
          return Command::INVALID;
        }
        array_shift($components);
        $userId = array_shift($components);
        array_shift($components);
        $mountPoint = Constants::PATH_SEP . implode(Constants::PATH_SEP, $components);
      }
    }

    $mounts = $this->mountMapper->findAll($userId);
    if (!empty($mountPoint)) {
      $mounts = array_filter($mounts, fn(ArchiveMount $mount) => $mount->getMountPointPath() == $mountPoint);
    }

    $previousUserId = null;
    $userFolder = null;

    /** @var ArchiveMount $mountEntity */
    foreach ($mounts as $mountEntity) {
      $userId = $mountEntity->getUserId();
      $userFolderPrefix = Constants::PATH_SEP . $userId . Constants::PATH_SEP . 'files';
      $mountPointPath = $userFolderPrefix . $mountEntity->getMountPointPath();
      $output->writeln('<info>' . 'Processing "' . $mountPointPath . '".' . '</info>');
      if ($previousUserId != $userId) {
        $previousUserId = $userId;
        try {
          $userFolder = $this->rootFolder->getUserFolder($userId);
        } catch (Throwable $t) {
          $userFolder = null;
          $output->writeln('<warn>' . 'Cannot access the home-folder of "' . $userId . '": ' . $t->getMessage() . '</warn>');
          $output->writeln('<warn>' . 'Skipping mount "' . Constants::PATH_SEP . $userId . Constants::PATH_SEP . 'files' . $mountEntity->getMountPointPath() . '".' . '</warn>');
          continue;
        }
      } elseif ($userFolder === null) {
        continue;
      }

      $archiveFiles = array_filter($userFolder->getById($mountEntity->getArchiveFileId()), fn(File $archiveFile) => $archiveFile->isReadable());
      if (empty($archiveFiles)) {
        $output->writeln('<warn>' . 'Cannot access the referenced archive-file "' . $mountEntity->getArchiveFilePath() . '".' . '</warn>');
        $output->writeln('<warn>' . 'Skipping mount "' . $mountPointPath . '".' . '</warn>');
        continue;
      }

      /** @var File $archiveFile */
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
        $output->writeln('<info>' . 'Archive file-path updated to "' . $archiveFilePath . '".' . '</info>');
      }

      // A stale root id would make the storage answer from the broken cache
      // and the scan would find nothing.
      $storedRootId = $mountEntity->getMountPointFileId();
      $mountEntity->setMountPointFileId(0);

      try {
        /** @var IMountPoint $mountPoint */
        $mountPoint = $this->mountProvider->getMountPoint($mountEntity, $userId, PHP_INT_MAX);

        $this->mountManager->addMount($mountPoint);
        $storage = $mountPoint->getStorage();
        $storage->getScanner()->scan('');
        $storageRootId = $mountPoint->getStorageRootId();
        $mountEntity->setMountPointFileId($storageRootId);
        if ($storageRootId != $storedRootId) {
          $this->mountMapper->update($mountEntity);
        }
      } catch (Throwable $t) {
        $output->writeln('<warn>' . 'Unable to update the file-cache for "' . $mountPointPath . '": ' . $t->getMessage() . '</warn>');
        $output->writeln('<warn>' . 'Skipping mount "' . $mountPointPath . '".' . '</warn>');
      }
    }

    return Command::SUCCESS;
  }
}
