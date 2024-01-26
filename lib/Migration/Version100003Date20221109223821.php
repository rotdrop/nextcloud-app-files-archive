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

use OCA\FilesArchive\Constants;

/**
 * Add encrypted archive password.
 */
class Version100003Date20221109223821 extends SimpleMigrationStep
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

    $table = $schema->getTable($this->appName . '_mounts');
    $table->addColumn('mount_flags', 'bigint', [
      'notnull' => true,
      'length' => 20,
      'default' => 0,
    ]);

    return $schema;
  }
}
