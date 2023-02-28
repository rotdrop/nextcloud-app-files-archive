<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine
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

use closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use OCP\IDBConnection;

use OCA\FilesArchive\Constants;

/** Initial database setup. */
class Version000000Date20221031100000 extends SimpleMigrationStep
{
  use \OCA\FilesArchive\Toolkit\Traits\AppNameTrait;

  /** @var string */
  private $appName;

  /** Constructor. */
  public function __construct()
  {
    // parent::__construct();
    $this->appName = $this->getAppInfoAppName(__DIR__);
  }

  /** {@inheritdoc} */
  public function changeSchema(IOutput $output, closure $schemaClosure, array $options)
  {
    /** @var ISchemaWrapper $schema */
    $schema = $schemaClosure();

    if (!$schema->hasTable($this->appName . '_mounts')) {
      $table = $schema->createTable($this->appName . '_mounts');
      $table->addColumn('id', 'bigint', [
        'autoincrement' => true,
        'notnull' => true,
        'length' => 20,
      ]);
      $table->addColumn('user_id', 'string', [
        'notnull' => true,
        'length' => 200,
      ]);
      $table->addColumn('mount_point_path', 'string', [
        'notnull' => true,
        'length' => 4000,
      ]);
      $table->addColumn('mount_point_path_hash', 'string', [
        'notnull' => true,
        'length' => 32,
      ]);
      $table->addColumn('archive_file_id', 'bigint', [
        'notnull' => true,
        'length' => 20,
      ]);
      $table->addColumn('archive_file_path', 'string', [
        'notnull' => true,
        'length' => 4000,
      ]);
      $table->addColumn('archive_file_path_hash', 'string', [
        'notnull' => true,
        'length' => 32,
      ]);

      $table->setPrimaryKey(['id']);
      $table->addIndex(['user_id'], 'user_id_index');
      $table->addIndex(['mount_point_path_hash'], 'mount_point_index');
      $table->addIndex(['archive_file_id', 'archive_file_path_hash'], 'archive_file_index');
    }
    return $schema;
  }
}
