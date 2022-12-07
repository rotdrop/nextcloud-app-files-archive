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
 *"
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\FilesArchive\Controller;

use OCA\RotDrop\Toolkit\Service\ArchiveService;

/** Trait class which provides the proposed target basenames. */
trait TargetPathTrait
{
  /** @var string */
  private $targetBaseNameTemplate;

  /** @var string */
  private $mountPointTemplate;

  /** @var ArchiveService */
  private $archiveService;

  /** @return string The default mount point name */
  private function defaultTargetBaseName():string
  {
    return str_replace(
      SettingsController::ARCHIVE_FILE_NAME_PLACEHOLDER,
      $this->archiveService->getArchiveFolderName(),
      $this->targetBaseNameTemplate,
    );
  }

  /** @return string The default mount point name */
  private function defaultMountPointName():string
  {
    return str_replace(
      SettingsController::ARCHIVE_FILE_NAME_PLACEHOLDER,
      $this->archiveService->getArchiveFolderName(),
      $this->mountPointTemplate,
    );
  }
}
