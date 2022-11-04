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

namespace OCA\FilesArchive\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/** Entity class for mounts table. */
class ArchiveMount extends Entity implements JsonSerializable
{
  public $id;
  protected $userId;

  protected $mountPointPath;
  protected $mountPointPathHash;

  protected $archiveFileId;
  protected $archiveFilePath;
  protected $archiveFilePathHash;

  /** CTOR */
  public function __construct()
  {
    // $this->addType('id', 'integer');
    $this->addType('userId', 'string');

    $this->addType('mountPointPath', 'string');
    $this->addType('mountPointPathHash', 'string');

    $this->addType('archiveFileId', 'integer');
    $this->addType('archiveFilePath', 'string');
    $this->addType('archiveFilePathHash', 'string');
  }

  /** {@inheritdoc} */
  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'userId' => $this->userId,

      'mountPointPath' => $this->mountPointPath,
      'mountPointPathHash' => $this->mountPointPathHash,

      'archiveFileId' => $this->archiveFileId,
      'archiveFilePath' => $this->archiveFilePath,
      'archiveFilePathHash' => $this->archiveFilePathHash,
    ];
  }
}
