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

/**
 * Base-class for exceptions thrown after the archive has been opened but is
 * otherwise invalid or dangerous to use.
 *
 * @see ArchiveTooLargeException
 */
class ArchiveInvalidException extends ArchiveException
{
  /**
   * @var array
   *
   * @see ArchiveService::archiveInfo()
   */
  protected $archiveInfo;

  /**
   * @param array $archiveInfo Info as obtained from
   * ArchiveService::archiveInfo().
   *
   * @param null|string $message
   *
   * @param null|Throwable $previous
   *
   * The exception code is tied to 0.
   */
  public function __construct(array $archiveInfo, ?string $message, ?Throwable $previous)
  {
    parent::__construct($message, 0, $previous);
    $this->archiveInfo = $archiveInfo;
  }

  /**
   * @return Archive-info array.
   *
   * @see ArchiveService::archiveInfo()
   */
  public function getArchiveInfo():array
  {
    return $this->archiveInfo;
  }
}
