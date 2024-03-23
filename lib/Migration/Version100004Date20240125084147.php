<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024 Claus-Justus Heine
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
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;
use OCP\IDBConnection;

use OCA\FilesArchive\Constants;

/**
 * Add encrypted archive password.
 */
class Version100004Date20240125084147 extends SimpleMigrationStep
{
  use \OCA\FilesArchive\Toolkit\Traits\AppNameTrait;

  /** @var string */
  private $appName;

  private string $tableName;

  private string $columnName;

  /**
   * @param IDBConnection $connection
   */
  public function __construct(
    protected IDBConnection $connection,
  ) {
    // parent::__construct();
    $this->appName = $this->getAppInfoAppName(__DIR__);
    $this->tableName = $this->appName . '_mounts';
    $this->columnName = 'mount_point_file_id';
  }

  /**
   * @param IOutput $output
   * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`.
   * @param array $options
   *
   * @return null|ISchemaWrapper
   */
  public function changeSchema(IOutput $output, Closure $schemaClosure, array $options):?ISchemaWrapper
  {
    /** @var ISchemaWrapper $schema */
    $schema = $schemaClosure();

    $table = $schema->getTable($this->tableName);

    if (!$table->hasColumn($this->columnName)) {
      $table->addColumn($this->columnName, 'bigint', [
        'notnull' => true,
        'default' => -1,
        'length' => 20,
      ]);
    }

    return $schema;
  }

  /**
   * @param IOutput $output
   * @param Closure $schemaClosure
   * @param array $options
   *
   * @return void
   */
  public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options):void
  {
    /** @var ISchemaWrapper $schema */
    $schema = $schemaClosure();
    if (!$schema->getTable($this->tableName)
        || !$schema->getTable($this->tableName)->hasColumn($this->columnName)) {
      return;
    }
    $selectQuery = $this->connection->getQueryBuilder();
    $selectQuery->select('*')->from($this->tableName);

    $fileIdQuery = $this->connection->getQueryBuilder();
    $fileIdQuery
      ->select('*')
      ->from('mounts')
      ->where($fileIdQuery->expr()->eq('mount_point', $fileIdQuery->createParameter('mount_point')));

    $updateQuery = $this->connection->getQueryBuilder();
    $updateQuery
      ->update($this->tableName)
      ->set($this->columnName, $updateQuery->createParameter($this->columnName))
      ->where($updateQuery->expr()->eq('id', $updateQuery->createParameter('id')));

    $deleteQuery = $this->connection->getQueryBuilder();
    $deleteQuery
      ->delete($this->tableName)
      ->where($deleteQuery->expr()->eq('id', $deleteQuery->createParameter('id')));

    $selectResult = $selectQuery->execute();
    while ($row = $selectResult->fetch()) {
      $mountPoint = Constants::PATH_SEPARATOR . $row['user_id'] . Constants::PATH_SEPARATOR . Constants::USER_FOLDER_PREFIX . $row['mount_point_path'] . Constants::PATH_SEPARATOR;

      $output->info('Searching file-id of mount point ' . $mountPoint . '.');

      $fileIdResult = $fileIdQuery
        ->setParameter('mount_point', $mountPoint)
        ->execute();
      switch ($fileIdResult->rowCount()) {
        case 0:
          $output->warning('Unable to find active mount of registered archive mount ' . $row['archive_file_path'] . ' -> ' . $mountPoint . '.');
          $deleteQuery
            ->setParameter('id', $row['id'])
            ->executeStatement();
          break;
        case 1:
          $rootId = (int)$fileIdResult->fetch()['root_id'];
          if ($rootId != $row[$this->columnName]) {
            $output->warning('Updating mount point file id to ' . $rootId . '.');
            $updateQuery->setParameter($this->columnName, $rootId);
            $updateQuery->setParameter('id', $row['id']);
            $updateQuery->executeStatement();
          } else {
            $output->info('Mount point file id already set to ' . $rootId . '.');
          }
          break;
        default:
          $rootIdRows = $fileIdResult->fetchAll();
          $output->warning('Multiple mount-point entries found for registered archive mount '.  $row['archive_file_path'] . ' -> ' . $mountPoint . '.');
          $output->warning(print_r($rootIdRows, true));
          $rootIdStorageId = [];
          foreach ($rootIdRows as $rootIdRow) {
            $rootIdStorageId[$rootIdRow['root_id'] . $rootIdRow['storage_id']] = true;
          }
          if (count($rootIdStorageId) == 1) {
            $rootId = $rootIdRows[0]['root_id'];
            $output->warning('The entries are redundant, using root-id ' . $rootId . '.');
          }
          break;
      }
      $fileIdResult->closeCursor();
    }
    $selectResult->closeCursor();
  }
}
