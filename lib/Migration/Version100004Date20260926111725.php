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

use OCA\FilesArchive\Db\ArchiveMountMapper;

/**
 * Remove the archive_file_path[_hash] columns as we now only work with the
 * file-id.
 */
class Version100004Date20260926111725 extends SimpleMigrationStep
{
  use \OCA\FilesArchive\Toolkit\Traits\AppNameTrait;

  /** CTOR */
  public function __construct()
  {
    $this->appName = $this->getAppInfoAppName(__DIR__);
  }

  /** {@inheritdoc} */
  #[Override]
  public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
  {
    /** @var ISchemaWrapper $schema */
    $schema = $schemaClosure();
    $table = $schema->getTable($this->appName . '_' . ArchiveMountMapper::TABLE_NAME);
    $table->dropColumn('archive_file_path');
    $table->dropColumn('archive_file_path_hash');

    return $schema;
  }
}
