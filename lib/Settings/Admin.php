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

namespace OCA\FilesArchive\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\IDelegatedSettings;

use OCA\FilesArchive\Constants;

/**
 * Render the administrative settings for this app.
 */
class Admin implements IDelegatedSettings
{
  use \OCA\FilesArchive\Toolkit\Traits\AssetTrait;

  const TEMPLATE = "admin-settings";

  /** @var string */
  private $appName;

  // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
  public function __construct(string $appName)
  {
    $this->appName = $appName;
    $this->initializeAssets(__DIR__);
  }
  // phpcs:enable

  /**
   * Return the HTML-template in order to render the personal settings.
   *
   * @return TemplateResponse
   */
  public function getForm()
  {
    return new TemplateResponse(
      $this->appName,
      self::TEMPLATE, [
        'appName' => $this->appName,
        'assets' => [
          Constants::JS => $this->getJSAsset(self::TEMPLATE),
          Constants::CSS => $this->getCSSAsset(self::TEMPLATE),
        ],
      ],
      'blank');
  }

  /**
   * @return string the section ID, e.g. 'sharing'
   * @since 9.1
   */
  public function getSection()
  {
    return $this->appName;
  }

  /**
   * @return int whether the form should be rather on the top or bottom of
   * the admin section. The forms are arranged in ascending order of the
   * priority values. It is required to return a value between 0 and 100.
   *
   * E.g.: 70
   * @since 9.1
   */
  public function getPriority()
  {
    return 50;
  }

  /**
   * {@inheritdoc}
   *
   * @return null|string
   */
  public function getName(): ?string
  {
    return null;
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   */
  public function getAuthorizedAppConfig(): array
  {
    return [];
  }
}

// Local Variables: ***
// c-basic-offset: 2 ***
// indent-tabs-mode: nil ***
// End: ***
