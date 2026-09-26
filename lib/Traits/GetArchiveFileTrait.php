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

namespace OCA\FilesArchive\Traits;

use OCP\Files\File;
use OCP\Files\Folder;
use OCA\FilesArchive\Db\ArchiveMount;

/**
 * Get one suitable File instance of the archive file given its file
 * id. Suitable at the time of this writing means "readable".
 */
trait GetArchiveFileTrait
{
  /**
   * Query the user folder for the archive file given its file id and return
   * one readable File instance referring to that file-id.
   *
   * @param Folder $userFolder
   *
   * @param ArchiveMount $mountEntity
   *
   * @return ?File
   */
  private function getArchiveFile(Folder $userFolder, ArchiveMount $mountEntity): ?File
  {
    $archiveFileId = $mountEntity->getArchiveFileId();
    $archiveFiles = array_filter($userFolder->getById($archiveFileId), fn(File $archiveFile) => $archiveFile->isReadable());
    if (count($archiveFiles) == 0) {
      return null;
    }

    return array_shift($archiveFiles);
  }
}
