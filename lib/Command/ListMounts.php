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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OCP\Files\File;
use OCP\Files\Folder;
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

/** List mounted archive files. */
class ListMounts extends Command
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
      ->setName($this->appName . ':list-mounts')
      ->setDescription('List mounted archive files.')
      ->addArgument('user', InputArgument::OPTIONAL, 'List only mounts of the given user.')
      ->addOption(
        'all',
        'a',
        InputOption::VALUE_NONE,
        'List all mounted archives of all users.',
      )
      ;
  }

  /** {@inheritdoc} */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $userId = $input->getArgument('user');
    $all = $input->getOption('all');

    if (empty($all) && empty($userId)) {
      $output->writeln('<error>' . 'Either a user-id or "--all" has to be given.' . '</error>');
      $output->writeln('');
      (new DescriptorHelper)->describe($output, $this);
      return Command::INVALID;
    }

    $outputTable = new Table($output)
      ->setHeaders([
        'UserId',
        'UserFolder',
        'ArchivePaths',
        'ArchiveFID',
        'Password',
        'MountPoint',
        'MountFID',
        'Status',
        'Flags',
      ]);
    $rows = [];

    $mounts = $this->mountMapper->findAll($userId);

    $previousUserId = null;
    $userFolder = null;

    /** @var ArchiveMount $mountEntity */
    foreach ($mounts as $mountEntity) {
      $userId = $mountEntity->getUserId();
      $row = [
        'UserId' => $userId,
        'UserFolder' => '[absent]',
        'ArchivePaths' => '[none]',
        'ArchiveFID' => $mountEntity->getArchiveFileId(),
        'Password' => !empty($mountEntity->getArchivePassPhrase()) ? 'set' : 'unset',
        'MountPoint' => $mountEntity->getMountPointPath(),
        'MountFID' => $mountEntity->getMountPointFileId(),
        'Status' => 'absent',
        'Flags' => ($mountEntity->getMountFlags() & ArchiveMount::MOUNT_FLAG_STRIP_COMMON_PATH_PREFIX) ? 'strip' : '',
      ];

      $userFolderPrefix = Constants::PATH_SEP . $userId . Constants::PATH_SEP . 'files';
      $mountPointPath = $userFolderPrefix . $mountEntity->getMountPointPath();
      if ($previousUserId != $userId) {
        $previousUserId = $userId;
        try {
          $userFolder = $this->rootFolder->getUserFolder($userId);
        } catch (Throwable $t) {
          $userFolder = null;
          $rows[] = $row;
          continue;
        }
      } elseif ($userFolder === null) {
        $rows[] = $row;
        continue;
      }
      $row['UserFolder'] = 'ok';

      $archiveFiles = array_filter($userFolder->getById($mountEntity->getArchiveFileId()), fn(File $archiveFile) => $archiveFile->isReadable());
      if (count($archiveFiles) > 0) {
        $row['ArchivePaths'] = implode(', ', array_map(fn(File $archiveFile) => substr($archiveFile->getPath(), strlen($userFolderPrefix)), $archiveFiles));
      }

      $mountPointFolders = $userFolder->getById($mountEntity->getMountPointFileId());
      /** @var Folder $mountPointFolder */
      foreach ($mountPointFolders as $mountPointFolder) {
        if ($mountPointFolder->getPath() == $mountPointPath && $mountPointFolder->isReadable()) {
          $row['Status'] = 'ok';
        }
      }
      $rows[] = $row;
    }

    $outputTable
      ->setRows($rows)
      ->render()
      ;

    return Command::SUCCESS;
  }
}
