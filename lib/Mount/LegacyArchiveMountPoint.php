<?php
/**
 * @author    Fabio Fantoni <fabio.fantoni@m2r.biz>
 * @copyright 2026 Fabio Fantoni
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

namespace OCA\FilesArchive\Mount;

// F I X M E internal
use OC\Files\Mount\MoveableMount;

/**
 * Like ArchiveMountPoint, but additionally implementing the private legacy
 * MoveableMount interface: Nextcloud < 34 marks a mount point as
 * movable/removable only through that interface, while Nextcloud >= 34
 * removed it in favour of the public IMovableMount. This class must only be
 * loaded when the legacy interface exists.
 */
class LegacyArchiveMountPoint extends ArchiveMountPoint implements MoveableMount
{
}
