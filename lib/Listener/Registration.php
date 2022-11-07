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

namespace OCA\FilesArchive\Listener;

use OCP\AppFramework\Bootstrap\IRegistrationContext;

/**
 * Static listener registration class.
 */
class Registration
{
  /**
   * Register all listeners.
   *
   * @param IRegistrationContext $context
   *
   * @return void
   */
  public static function register(IRegistrationContext $context):void
  {
    self::registerListener($context, FilesActionListener::class);
    self::registerListener($context, FileNodeListener::class);
  }

  /**
   * @param IRegistrationContext $context
   *
   * @param string $class
   *
   * @return void
   */
  private static function registerListener(IRegistrationContext $context, string $class):void
  {
    $events = $class::EVENT;
    if (!is_array($events)) {
      $events = [ $events ];
    }
    foreach ($events as $event) {
      $context->registerEventListener($event, $class);
    }
  }
}
