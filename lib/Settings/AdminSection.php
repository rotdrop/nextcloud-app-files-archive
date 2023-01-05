<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023 Claus-Justus Heine <himself@claus-justus-heine.de>
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

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

/**
 * Define the configuration section for administrative settings.
 */
class AdminSection implements IIconSection
{
  /** @var string */
  private $appName;

  /** @var IL10N */
  private $l;

  /** @var IURLGenerator */
  private $urlGenerator;

  // phpcs:disable Squiz.Commenting.FunctionComment.Missing
  public function __construct(
    string $appName,
    IL10N $l10n,
    IURLGenerator $urlGenerator,
  ) {
    $this->appName = $appName;
    $this->l = $l10n;
    $this->urlGenerator = $urlGenerator;
  }
  // phpcs:enable

  /**
   * Return the ID of the section. It is supposed to be a lower case string
   *
   * @return string
   */
  public function getID()
  {
    return $this->appName;
  }

  /**
   * Return the translated name as it should be displayed, e.g. 'LDAP / AD
   * integration'. Use the L10N service to translate it.
   *
   * @return string
   */
  public function getName()
  {
    return $this->l->t('Archive Explorer');
  }

  /**
   * @return int whether the form should be rather on the top or bottom of
   * the settings navigation. The sections are arranged in ascending order of
   * the priority values. It is required to return a value between 0 and 99.
   */
  public function getPriority()
  {
    return 50;
  }

  /**
   * @return The relative path to a an icon describing the section
   */
  public function getIcon()
  {
    return $this->urlGenerator->imagePath($this->appName, 'app-dark.svg');
  }
}

// Local Variables: ***
// c-basic-offset: 2 ***
// indent-tabs-mode: nil ***
// End: ***
