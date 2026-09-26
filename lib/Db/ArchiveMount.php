<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024, 2026 Claus-Justus Heine
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

use Spatie\TypeScriptTransformer\Attributes as TSAttributes;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/**
 * Entity class for mounts table.
 *
 * @method public int getId()
 * @method public string getUserId()
 * @method public void setUserId(string $userId)
 *
 * @method public int getMountPointFileId()
 * @method public void setMountPointFileId(int $fileId)
 * @method public string getMountPointPath()
 * @method public void setMountPointPath(string $path)
 * @method public string getMountPointPathHash()
 * @method public void setMountPointPathHash(string $pathHash)
 *
 * @method public int getArchiveFileId()
 * @method public void setArchiveFileId(int $id)
 *
 * @method public string getArchivePassPhrase()
 * @method public void setArchivePathPhase(string $passPhrase)
 *
 * @method public int getMountFlags()
 * @method public void setMountFlags(int $flags)
 */
#[TSAttributes\TypeScript]
#[TSAttributes\TypeScriptTransformer(\OCA\RotDrop\DevScripts\PhpToTypeScript\DatabaseEntityTransformer::class)]
class ArchiveMount extends Entity implements JsonSerializable
{
  public const MOUNT_FLAG_STRIP_COMMON_PATH_PREFIX = (1 << 0);
  public const MOUNT_FLAGS_MASK = self::MOUNT_FLAG_STRIP_COMMON_PATH_PREFIX;

  /** @var int */
  public $id;

  /** @var string */
  protected $userId;

  /**
   * @var int
   * file-ids are strings to prevent overflow in the JS frontend.
   */
  #[TSAttributes\TypeScriptType('string')]
  protected $mountPointFileId;

  /** @var string */
  protected $mountPointPath;

  /** @var string */
  protected $mountPointPathHash;

  /**
   * @var int
   * file-ids are strings to prevent overflow in the JS frontend.
   */
  #[TSAttributes\TypeScriptType('string')]
  protected $archiveFileId;

  /** @var ?string */
  protected $archivePassPhrase;

  /** @var int */
  protected $mountFlags;

  /** CTOR */
  public function __construct()
  {
    // $this->addType('id', 'integer');
    $this->addType('userId', 'string');

    $this->addType('mountPointFileId', 'integer');
    $this->addType('mountPointPath', 'string');
    $this->addType('mountPointPathHash', 'string');

    $this->addType('archiveFileId', 'integer');

    $this->addType('archivePassPhrase', 'string');

    $this->addType('mountFlags', 'integer');
  }

  /**
   * Set the mount point path and automatically also the hash value for it.
   *
   * @param string $path
   *
   * @return void
   */
  public function setMountPointPath(string $path):void
  {
    parent::setMountPointPath($path);
    parent::setMountPointPathHash(md5($path));
  }

  /** {@inheritdoc} */
  public function jsonSerialize():mixed
  {
    return [
      'id' => $this->id,
      'userId' => $this->userId,

      'mountPointFileId' => (string)$this->mountPointFileId, // convert to string to avoid integer overflow with JS
      'mountPointPath' => $this->mountPointPath,
      'mountPointPathHash' => $this->mountPointPathHash,

      'archiveFileId' => (string)$this->archiveFileId, // convert to string to avoid integer overflow with JS

      'archivePassPhrase' => $this->archivePassPhrase,

      'mountFlags' => $this->mountFlags,
    ];
  }
}
