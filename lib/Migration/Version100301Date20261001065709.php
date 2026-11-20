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

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;
use Override;

// Nextcloud >= 35 replays the migrations of *disabled* apps during an upgrade
// (OC\DB\SchemaChecker::applyDisabledMigrations()). It includes the migration
// files directly and does not register the class loader of the disabled app.
// PHP binds traits when the class is declared, so a trait that cannot be
// autoloaded is a fatal error which cannot be caught and aborts "occ upgrade".
// Make sure the class loader of this app is available before the class below
// is declared.
include_once dirname(__DIR__, 2) . '/composer/autoload.php';

/**
 * Drop potential left-over index.
 */
class Version100301Date20261001065709 extends SimpleMigrationStep
{
  use TableNameTrait;

  /** {@inheritdoc} */
  #[Override]
  public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
  {
    /** @var ISchemaWrapper $schema */
    $schema = $schemaClosure();
    $table = $schema->getTable($this->getTableName());
    if ($table->hasIndex('archive_file_index')) {
      $table->dropIndex('archive_file_index');
    }
    $table->addIndex(['archive_file_id'], 'archive_file_index');

    return $schema;
  }
}
