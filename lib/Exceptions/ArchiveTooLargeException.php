<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
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

namespace OCA\FilesArchive\Exceptions;

use Throwable;

use OCA\RotDrop\Toolkit\Service\ArchiveService;

/**
 * Exception thrown if the archive exceeds the configured limit.
 */
class ArchiveTooLargeException extends ArchiveInvalidException
{
  private int $limit;

  /**
   * @param string $message Custom error message, preferrably translated.
   *
   * @param int $limit The configured limit which was exceeded.
   *
   * @param array $archiveInfo As obtained from ArchiveService::getArchiveInfo().
   *
   * @param null|Throwable $previous
   */
  public function __construct(
    string $message,
    int $limit,
    array $archiveInfo,
    ?Throwable $previous = null,
  ) {
    parent::__construct($archiveInfo, $message, $previous);
    $this->limit = $limit;
  }

  /**
   * Return the configured limit.
   *
   * @return int
   */
  public function getLimit():int
  {
    return $this->limit;
  }

  /**
   * Return the actual uncompressed size of the archive.
   *
   * @return int
   */
  public function getActualSize():int
  {
    return $this->archiveInfo[ArchiveService::ARCHIVE_INFO_ORIGINAL_SIZE];
  }
}
