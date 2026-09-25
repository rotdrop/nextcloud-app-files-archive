<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2026,  Claus-Justus Heine
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

use Closure;
use Exception;
use OCP\DB\ISchemaWrapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;
use OC\Files\Cache\Storage as StorageCache;
use Override;
use Throwable;

use OCA\FilesArchive\Constants;
use OCA\FilesArchive\Db\ArchiveMount;
use OCA\FilesArchive\Db\ArchiveMountMapper;

/**
 * Replace legacy storage ids which contain the archive-file paths by the current variant which just contains the file-id.
 */
class Version100004Date20260923184215 extends SimpleMigrationStep
{
  use \OCA\FilesArchive\Storage\StorageIdTrait;
  use \OCA\FilesArchive\Toolkit\Traits\AppNameTrait;

  /**
   * @param IDBConnection $connection
   *
   * @param ArchiveMountMapper $mapper
   */
  public function __construct(
    protected IDBConnection $connection,
    protected ArchiveMountMapper $mapper,
  ) {
    $this->appName = $this->getAppInfoAppName(__DIR__);
  }

  /** {@inheritdoc} */
  #[Override]
  public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void
  {
  }

  /** {@inheritdoc} */
  #[Override]
  public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
  {
    return null;
  }

  /**
   * @param ArchiveMount $mount
   *
   * @return string
   */
  private function getLegacyStorageId(ArchiveMount $mount): string
  {
    return $this->appName . ':'
      . Constants::PATH_SEP . $mount->getUserId()
      . Constants::PATH_SEP . 'files'
      . $mount->getArchiveFilePath()
      . Constants::PATH_SEP;
  }

  /** {@inheritdoc} */
  #[Override]
  public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void
  {
    $mounts = $this->mapper->findAll();
    $storageIds = array_map(
      fn(ArchiveMount $mount) => $this->getLegacyStorageId($mount),
      $mounts,
    );
    $archiveFileIds = array_map(
      fn(ArchiveMount $mount) => $mount->getArchiveFileId(),
      $mounts,
    );
    $storageIds = array_map(
      fn(string $rawStorageId) => StorageCache::adjustStorageId($rawStorageId),
      array_combine($archiveFileIds, $storageIds),
    );

    $this->connection->beginTransaction();
    try {
      foreach ($storageIds as $fileId => $storageId) {
        $replaceQuery = $this->connection->getQueryBuilder();
        $replaceQuery
          ->update('storages')
          ->set('id', $replaceQuery->createParameter('storageId'))
          ->where($replaceQuery->expr()->eq('id', $replaceQuery->createParameter('legacyStorageId')))
          ->setParameter('storageId', $this->getStorageId($fileId), IQueryBuilder::PARAM_STR)
          ->setParameter('legacyStorageId', $storageId, IQueryBuilder::PARAM_STR);
        $replaceQuery->executeStatement();
      }

      $this->connection->commit();
    } catch (Throwable $t) {
      if ($this->connection->inTransaction()) {
        $this->connection->rollBack();
      }
      throw new Exception('Migrating from legacy storage ids to consistent storage ids failed', 0, $t);
    }
  }
}
