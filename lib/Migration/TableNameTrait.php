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

use OCA\FilesArchive\Db\ArchiveMountMapper;

/** Simple helper for the table name. */
trait TableNameTrait
{
  use \OCA\FilesArchive\Toolkit\Traits\AppNameTrait;

  /** @return string */
  protected function getTableName(): string
  {
    $appName = $this->getAppInfoAppName(__DIR__);
    $tableName = $appName . '_' . ArchiveMountMapper::TABLE_NAME;

    return $tableName;
  }
}
