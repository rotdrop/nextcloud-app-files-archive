<?php
/**
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

use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Db\ArchiveMount;

/**
 * Bring already stored mount-point paths into canonical form.
 *
 * Older versions stored whatever the client sent, so rows may contain a
 * doubled leading slash ("//name", produced by the web UI when mounting in the
 * file-space root) or a relative prefix ("./name", produced by callers that
 * omit the leading slash). ArchiveMount::setMountPointPath() normalizes new
 * values; this step fixes the rows that are already in the database, and also
 * repairs a mount_point_path_hash that does not match its path.
 *
 * The step works on the raw columns instead of going through the entity: that
 * keeps it cheap (no passphrase decryption, no per-row hydration) and lets it
 * see the stored hash, which the mapper replaces with a recomputed one while
 * reading.
 *
 * It is idempotent - a second run finds nothing to do.
 */
class NormalizeMountPointPaths implements IRepairStep
{
  private const TABLE = 'files_archive_mounts';

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected IDBConnection $db,
  ) {
  }
  // phpcs:enable

  /** {@inheritdoc} */
  public function getName()
  {
    return 'Normalize stored archive mount-point paths';
  }

  /** {@inheritdoc} */
  public function run(IOutput $output)
  {
    $rows = $this->db->getQueryBuilder()
      ->select('id', 'user_id', 'mount_point_path', 'mount_point_path_hash')
      ->from(self::TABLE)
      ->orderBy('id', 'ASC')
      ->executeQuery()
      ->fetchAll();

    if (count($rows) == 0) {
      return;
    }

    $fixed = 0;
    $skipped = 0;
    /** @var array<string,true> $seen normalized path per user */
    $seen = [];

    foreach ($rows as $row) {
      $id = (int)$row['id'];
      $userId = (string)$row['user_id'];
      $storedPath = (string)$row['mount_point_path'];
      $storedHash = (string)$row['mount_point_path_hash'];

      try {
        $normalizedPath = ArchiveMount::normalizeMountPointPath($storedPath);
      } catch (Throwable $t) {
        // e.g. a path containing ".." segments: leave it alone, a human has to
        // decide what to do with it.
        $output->warning(
          'Cannot normalize the mount-point path of mount ' . $id
          . ' ("' . $storedPath . '"): ' . $t->getMessage(),
        );
        ++$skipped;
        continue;
      }

      // A mount on the file-space root is not something the app can produce
      // (the mount point always has a basename); treat it as corrupt instead of
      // writing a path that would shadow the whole home folder.
      if ($normalizedPath === Constants::PATH_SEP) {
        $output->warning(
          'Mount ' . $id . ' of user "' . $userId . '" has an empty mount-point path'
          . ' ("' . $storedPath . '"), skipping it.',
        );
        ++$skipped;
        continue;
      }

      // Two rows can normalize to the same path ("//name" and "./name"). Do not
      // delete either of them here - that is a decision for an administrator -
      // but make sure the collision is reported.
      $seenKey = $userId . "\0" . $normalizedPath;
      if (isset($seen[$seenKey])) {
        $output->warning(
          'Mount ' . $id . ' of user "' . $userId . '" collides with another mount after'
          . ' normalization ("' . $normalizedPath . '"), skipping it.',
        );
        ++$skipped;
        continue;
      }
      $seen[$seenKey] = true;

      $expectedHash = md5($normalizedPath);
      if ($normalizedPath === $storedPath && $expectedHash === $storedHash) {
        continue;
      }

      try {
        $qb = $this->db->getQueryBuilder();
        $qb->update(self::TABLE)
          ->set('mount_point_path', $qb->createNamedParameter($normalizedPath))
          ->set('mount_point_path_hash', $qb->createNamedParameter($expectedHash))
          ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, \PDO::PARAM_INT)))
          ->executeStatement();
      } catch (Throwable $t) {
        $output->warning('Cannot update mount ' . $id . ': ' . $t->getMessage());
        ++$skipped;
        continue;
      }

      if ($normalizedPath !== $storedPath) {
        $output->info(
          'Mount ' . $id . ' of user "' . $userId . '": "' . $storedPath
          . '" -> "' . $normalizedPath . '"',
        );
      } else {
        $output->info(
          'Mount ' . $id . ' of user "' . $userId . '": repaired the path hash of "'
          . $normalizedPath . '"',
        );
      }
      ++$fixed;
    }

    if ($fixed > 0) {
      $output->info('Normalized ' . $fixed . ' archive mount(s).');
    }
    if ($skipped > 0) {
      $output->warning('Skipped ' . $skipped . ' archive mount(s), see the warnings above.');
    }
  }
}
