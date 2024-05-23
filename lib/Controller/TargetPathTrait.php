<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024 Claus-Justus Heine <himself@claus-justus-heine.de>
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

use OCA\FilesArchive\Toolkit\Service\ArchiveService;
use OCA\FilesArchive\Constants;

/** Trait class which provides the proposed target basenames. */
trait TargetPathTrait
{
  /** @var string */
  private string $targetBaseNameTemplate;

  /** @var string */
  private string $mountPointTemplate;

  /**
   * @param string $archiveFileName
   *
   * @return string The default mount point name
   */
  private function defaultTargetBaseName(string $archiveFileName):string
  {
    return str_replace(
      SettingsController::ARCHIVE_FILE_NAME_PLACEHOLDER,
      ArchiveService::getArchiveFolderName($archiveFileName),
      $this->targetBaseNameTemplate,
    );
  }

  /**
   * @param string $archiveFileName
   *
   * @return string The default mount point name
   */
  private function defaultMountPointName(string $archiveFileName):string
  {
    return str_replace(
      SettingsController::ARCHIVE_FILE_NAME_PLACEHOLDER,
      ArchiveService::getArchiveFolderName($archiveFileName),
      $this->mountPointTemplate,
    );
  }

  /**
   * @param string $destinationPath
   *
   * @param string $archivePath
   *
   * @param string $operation One of 'mount' or 'extract'
   *
   * @return array
   * ``` [ 'path' => PATH, 'baseName' => BASE_NAME, 'dirName' => DIR_NAME ]```.
   */
  private function targetPathInfo(?string $destinationPath, string $archivePath, string $operation):array
  {
    if (empty($destinationPath)) {
      $destinationDirName = dirname($archivePath);
      if ($operation == 'mount') {
        $destinationBaseName = $this->defaultMountPointName($archivePath);
      } else {
        $destinationBaseName = $this->defaultTargetBaseName($archivePath);
      }
      $destinationPath = $destinationDirName . Constants::PATH_SEPARATOR . $destinationBaseName;
    } else {
      $destinationInfo = pathinfo($destinationPath);
      $destinationBaseName = $destinationInfo['basename'];
      $destinationDirName = $destinationInfo['dirname'];
    }
    return [
      'baseName' => $destinationBaseName,
      'dirName' => $destinationDirName,
      'path' => $destinationPath,
    ];
  }
}
