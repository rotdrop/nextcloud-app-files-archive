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

use OCA\FilesArchive\Toolkit\DTO\AbstractResponseDTO;
use OCA\FilesArchive\Controller\DTO\DiagnosticsResponse\CSS;

/** DTO for the two end-points of the DiagnosticsController. */
class DiagnosticsResponse extends AbstractResponseDTO
{
  /** {@inheritdoc} */
  public function __construct(
    public readonly string $html,
    public readonly CSS $css,
  ) {
  }
}
