<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024 Claus-Justus Heine <himself@claus-justus-heine.de>
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

namespace OCA\FilesArchive\Service;

use OCP\IL10N;
use Psr\Log\LoggerInterface as ILogger;
use OCP\Files\File;

use OCA\FilesArchive\Toolkit\Service\ArchiveService;

/** Used in order to construct a new instance of the ArchiveService class. */
class ArchiveServiceFactory
{
  /** @var array<int, ArchiveService> */
  private array $cache = [];

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    protected ILogger $logger,
    protected IL10N $l,
  ) {
  }
  // phpcs:enable

  /**
   * Create one unique service instance for each distinct archive file.
   *
   * @param File $file
   *
   * @return ArchiveService
   */
  public function get(File $file):ArchiveService
  {
    $fileId = $file->getId();
    if (empty($this->cache[$fileId])) {
      $this->cache[$fileId] = new ArchiveService($this->logger, $this->l);
    }
    return $this->cache[$fileId];
  }
}
