<?php
/**
 * Archive Manager for Nextcloud
 *
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

namespace OCA\FilesArchive\Command;

use Throwable;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use OCP\Files\File;
use OCP\Files\IRootFolder;
use OCP\Files\Mount\IMountManager;
use OCP\IUserManager;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;

/**
 * Remove archive mounts whose archive file is no longer readable for the
 * owning user.
 *
 * The FileNodeListener already does this when an archive file is deleted
 * through a user session, but a file that expires from the trash bin is
 * removed by a background job without any session, so those mounts would
 * linger forever.
 */
class PruneMounts extends Command
{
  public function __construct(
    private IRootFolder $rootFolder,
    private IMountManager $mountManager,
    private IUserManager $userManager,
    private ArchiveMountMapper $mountMapper,
  ) {
    parent::__construct();
  }

  /** {@inheritdoc} */
  protected function configure()
  {
    $this->setName('files_archive:prune-mounts')
      ->setDescription('Remove archive mounts whose archive file is gone or unreadable')
      ->addOption(
        'dry-run',
        null,
        InputOption::VALUE_NONE,
        'Only report what would be removed',
      )
      ->addOption(
        'user',
        null,
        InputOption::VALUE_REQUIRED,
        'Restrict to the given user id',
      );
    parent::configure();
  }

  /** {@inheritdoc} */
  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $dryRun = (bool)$input->getOption('dry-run');
    $onlyUser = $input->getOption('user');

    $mounts = $this->mountMapper->findAll($onlyUser ?: null);

    $removed = 0;
    $kept = 0;
    $failed = 0;

    /** @var ArchiveMount $mountEntity */
    foreach ($mounts as $mountEntity) {
      $userId = $mountEntity->getUserId();
      try {
        $user = $this->userManager->get($userId);
        if ($user === null) {
          $output->writeln('<comment>skip</comment> mount #' . $mountEntity->getId()
            . ': user "' . $userId . '" does not exist');
          ++$failed;
          continue;
        }
        $userFolder = $this->rootFolder->getUserFolder($userId);
        $candidates = array_filter(
          $userFolder->getById($mountEntity->getArchiveFileId()),
          fn ($node) => $node instanceof File && $node->isReadable(),
        );
        if (count($candidates) > 0) {
          ++$kept;
          continue;
        }
      } catch (Throwable $t) {
        $output->writeln('<comment>skip</comment> mount #' . $mountEntity->getId()
          . ': ' . $t->getMessage());
        ++$failed;
        continue;
      }

      $mountPoint = Constants::PATH_SEPARATOR . $userId . Constants::PATH_SEPARATOR . 'files'
        . ArchiveMount::normalizeMountPointPath($mountEntity->getMountPointPath());

      if ($dryRun) {
        $output->writeln('<info>would remove</info> ' . $mountPoint
          . ' (archive file id ' . $mountEntity->getArchiveFileId() . ')');
        ++$removed;
        continue;
      }

      try {
        $this->mountManager->removeMount($mountPoint);
      } catch (Throwable $t) {
        // The mount is not registered in this process; that is fine, the DB
        // row is the authoritative state.
      }
      $this->mountMapper->delete($mountEntity);
      $output->writeln('<info>removed</info> ' . $mountPoint
        . ' (archive file id ' . $mountEntity->getArchiveFileId() . ')');
      ++$removed;
    }

    $output->writeln('');
    $output->writeln(sprintf(
      'kept: %d, %s: %d, skipped: %d',
      $kept,
      $dryRun ? 'would remove' : 'removed',
      $removed,
      $failed,
    ));

    return Command::SUCCESS;
  }
}
