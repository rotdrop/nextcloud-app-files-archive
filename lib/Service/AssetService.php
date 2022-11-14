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

namespace OCA\FilesArchive\Service;

use OCP\IL10N;
use Psr\Log\LoggerInterface as ILogger;

use OCA\FilesArchive\Exceptions;

/**
 * Return JavaScript- and CSS-assets names dealing with the attached content
 * hashes
 */
class AssetService
{
  use \OCA\RotDrop\Traits\LoggerTrait;

  const ASSET_META = __DIR__ . '/../../js/asset-meta.json';
  const JS = 'js';
  const CSS = 'css';
  const ASSET = 'asset';
  const HASH = 'hash';

  /** @var IL10N */
  protected $l;

  /** @var array */
  private $assets = [];

  // phpcs:ignore PEAR.Commenting.FunctionComment.Missing
  public function __construct(IL10N $l10n, ILogger $logger)
  {
    $this->logger = $logger;
    $this->l = $l10n;
    $metaJson = file_get_contents(self::ASSET_META);
    $assetMeta = json_decode($metaJson, true);
    foreach ([self::JS, self::CSS] as $type) {
      $this->assets[$type] = [];
      foreach (($assetMeta[$type] ?? []) as $assetFileName) {
        $assetFileName = basename($assetFileName, '.' . $type);
        if (preg_match('/^(.*)-([a-f0-9]+)$/', $assetFileName, $matches)) {
          ${self::ASSET} = $matches[0];
          $base = $matches[1];
          ${self::HASH} = $matches[2];
        } else {
          ${self::ASSET} = $assetFileName;
          $base = $assetFileName;
          ${self::HASH} = '';
        }
        $this->assets[$type][$base] = compact(self::ASSET, self::HASH);
      }
    }
  }


  /**
   * @param string $type js or css
   *
   * @param string $baseName
   *
   * @return array
   */
  public function getAsset($type, $baseName):array
  {
    if (empty($this->assets[$type][$baseName])) {
      throw new Exceptions\EnduserNotificationException(
        $this->l->t('Installation problem; the required %s-resource "%s" is not installed on the server,
 please contact the system administrator!', [
          $type, $baseName,
        ]));
    }
    return $this->assets[$type][$baseName];
  }

  /**
   * @param string $baseName
   *
   * @return string
   */
  public function getJSAsset($baseName)
  {
    return $this->getAsset(self::JS, $baseName);
  }

  /**
   * @param string $baseName
   *
   * @return string
   */
  public function getCSSAsset($baseName)
  {
    return $this->getAsset(self::CSS, $baseName);
  }
}
