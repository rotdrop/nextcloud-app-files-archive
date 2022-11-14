<?php
/**
 * A collection of reusable traits classes for Nextcloud apps.
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

namespace OCA\RotDrop\Toolkit\Traits;

use OCP\IGroupManager;

/**
 * Utitlity trait in order to get the addresses of the cloud admins.
 */
trait CloudAdminTrait
{
  /**
   * Contact information for the overall admins.
   *
   * @param IGroupManager $groupManager
   * @param bool $implode
   *
   * @return array
   */
  protected function getCloudAdminContacts(IGroupManager $groupManager, bool $implode = false):string
  {
    $adminGroup = $groupManager->get('admin');
    $adminUsers = $adminGroup->getUsers();
    $contacts = [];
    foreach ($adminUsers as $adminUser) {
      $contacts[] = [
        'name' => $adminUser->getDisplayName(),
        'email' => $adminUser->getEmailAddress(),
      ];
    }

    if ($implode) {
      $adminEmail = [];
      foreach ($contacts as $admin) {
        $adminEmail[] = empty($admin['name']) ? $admin['email'] : $admin['name'].' <'.$admin['email'].'>';
      }
      $contacts = implode(',', $adminEmail);
    }

    return $contacts;
  }
}
