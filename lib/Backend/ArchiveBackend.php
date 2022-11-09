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

namespace OCA\FilesArchive\Backend;

use InvalidArgumentException;
use wapmorgan\UnifiedArchive;
use wapmorgan\UnifiedArchive\Drivers\BasicDriver;

use OCA\FilesArchive\Backend\ArchiveFormats as Formats;

/**
 * Overload UnifiedArchive\UnifiedArchive with the goal to tweak the driver
 * selection.
 */
class ArchiveBackend extends UnifiedArchive\UnifiedArchive
{
  /**
   * @var array
   *
   * Define a potentially configurable driver ranking. The one with the
   * highest ranking will be picked first.
   */
  protected static $driverRanking = [
    UnifiedArchive\Drivers\AlchemyZippy::class => 0,
    UnifiedArchive\Drivers\Zip::class => 10,
    UnifiedArchive\Drivers\SevenZip::class => 20,
    UnifiedArchive\Drivers\NelexaZip::class => 30,
  ];

  /** {@inheritdoc} */
  public static function open($fileName, $abilities = [], $password = null)
  {
    if (!file_exists($fileName) || !is_readable($fileName)) {
      throw new InvalidArgumentException('Could not open file: ' . $fileName.' is not readable');
    }

    $format = Formats::detectArchiveFormat($fileName);
    if ($format === false) {
      return null;
    }

    if (!empty($abilities) && is_string($abilities)) {
      $password = $abilities;
      $abilities = [];
    }

    if (empty($abilities)) {
      $abilities = [BasicDriver::OPEN];
      if (!empty($password)) {
        $abilities[] = BasicDriver::OPEN_ENCRYPTED;
      }
    }

    $formatDrivers = Formats::getFormatDrivers($format, $abilities);
    if (empty($formatDrivers)) {
      return null;
    }
    usort($formatDrivers, fn(string $formatA, string $formatB) => -((static::$driverRanking[$formatA] ?? 0) <=> (static::$driverRanking[$formatB] ?? 0)));

    $driver = $formatDrivers[0];

    return new static($fileName, $format, $driver, $password);
  }
}
