<?php
/**
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\Storage;

// F I X M E: those are not public, but ...
use OC\Files\Storage\Common as AbstractStorage;
use OC\Files\Storage\PolyFill\CopyDirectory;

use OCP\AppFramework\IAppContainer;

use OCP\Files\FileInfo;
use OCP\Files\File;

/** Virtual storage mapping an archive file into the user file-space. */
class ArchiveStorage extends AbstractStorage
{
  use CopyDirectory;

  /** @var IAppContainer */
  protected $appContainer;

  /** @var File */
  protected $archiveFile;

  public function __construct(
    string $appName,
    IAppContainer $appContainer,
    File $archiveFile,
  ) {
    parent::__construct([]);
    $this->appContainer = $appContainer;
    $this->archiveFile = $archiveFile;
  }
}
