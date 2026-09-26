<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022-2026 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *"
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\FilesArchive\Controller\DTO;

use Spatie\TypeScriptTransformer\Attributes as TSAttributes;

/** Response DTO for ArchiveController info end-point. */
class ArchiveInfoResponse extends \OCA\FilesArchive\Toolkit\DTO\AbstractResponseDTO
{
  /** {@inheritdoc} */
  public function __construct(
    /** @var array<string> */
    public readonly array $messages,
    #[TSAttributes\LiteralTypeScriptType(
      ArchiveService::class . '.ARCHIVE_STATUS_OK'
        . '|' . ArchiveService::class . '.ARCHIVE_STATUS_TOO_LARGE'
        . '|' . ArchiveService::class . '.ARCHIVE_STATUS_BOMB'
    )]
    public readonly int $archiveStatus,
    public readonly ?ArchiveInfo $archiveInfo,
  ) {
  }
}
