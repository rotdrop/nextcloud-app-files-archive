<?php
/**
 * Archive Manager for Nextcloud
 *
 * @author    Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2024 Claus-Justus Heine <himself@claus-justus-heine.de>
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

return [
  'routes' => [
    [
      'name' => 'settings#set_admin',
      'url' => '/settings/admin/{setting}',
      'verb' => 'POST',
    ],
    [
      'name' => 'settings#get_admin',
      'url' => '/settings/admin/{setting}',
      'verb' => 'GET',
      'requirements' => [
        'setting' => '^.+$',
      ],
    ],
    [
      'name' => 'settings#get_admin',
      'url' => '/settings/admin',
      'verb' => 'GET',
      'postfix' => '.all',
    ],
    [
      'name' => 'settings#set_personal',
      'url' => '/settings/personal/{setting}',
      'verb' => 'POST',
    ],
    [
      'name' => 'settings#get_personal',
      'url' => '/settings/personal/{setting}',
      'verb' => 'GET',
      'requirements' => [
        'setting' => '^.+$',
      ],
    ],
    [
      'name' => 'settings#get_personal',
      'url' => '/settings/personal',
      'verb' => 'GET',
      'postfix' => '.all',
    ],
    // mount the given archive file
    [
      'name' => 'mount#mount',
      'url' => '/archive/mount/{archivePath}/{mountPointPath}',
      'verb' => 'POST',
      'defaults' => [
        'mountPointPath' => null,
      ],
    ],
    // schedule a mount or extraction operation
    [
      'name' => 'background_job#schedule',
      'url' => '/archive/schedule/{operation}/{archivePath}/{destinationPath}',
      'verb' => 'POST',
      'defaults' => [
        'destinationPath' => null,
      ],
    ],
    // cancel a mount or extraction operation
    [
      'name' => 'background_job#cancel',
      'url' => '/archive/schedule/{operation}/{archivePath}/{destinationPath}',
      'verb' => 'DELETE',
      'defaults' => [
        'destinationPath' => null,
      ],
    ],
    // retrieve the list of pending jobs, or the status for the given archive
    [
      'name' => 'background_job#status',
      'url' => '/archive/schedule/{operation}/{archivePath}/{destinationPath}',
      'verb' => 'GET',
      'defaults' => [
        'destinationPath' => null,
      ],
    ],
    // patch the passphrase into the mounts for the given archive
    [
      'name' => 'mount#patch',
      'url' => '/archive/mount/{archivePath}',
      'verb' => 'PATCH',
    ],
    // unmount all mounts for the given archive
    [
      'name' => 'mount#unmount',
      'url' => '/archive/unmount/{archivePath}',
      'verb' => 'POST',
    ],
    // get the status of the given archive mount
    [
      'name' => 'mount#mount_status',
      'url' => '/archive/mount/{archivePath}',
      'verb' => 'GET',
    ],
    // archive file management
    [
      'name' => 'archive#info',
      'url' => '/archive/info/{archivePath}',
      'verb' => 'POST',
    ],
    // archive extraction
    [
      'name' => 'archive#extract',
      'url' => '/archive/extract/{archivePath}/{targetPath}',
      'verb' => 'POST',
    ],
    // diagnostics output
    [
      'name' => 'diagnostics#archiveFormats',
      'url' => '/diagnostics/archive/formats',
      'verb' => 'GET',
    ],
    [
      'name' => 'diagnostics#archiveDrivers',
      'url' => '/diagnostics/archive/drivers',
      'verb' => 'GET',
    ],
    [
      'name' => 'diagnostics#archiveFormat',
      'url' => '/diagnostics/archive/format/{format}',
      'verb' => 'GET',
    ],
    /**
     * Attempt a catch all ...
     */
    [
      'name' => 'catch_all#post',
      'postfix' => 'post',
      'url' => '/{a}/{b}/{c}/{d}/{e}/{f}/{g}',
      'verb' => 'POST',
      'defaults' => [
        'a' => '',
        'b' => '',
        'c' => '',
        'd' => '',
        'e' => '',
        'f' => '',
        'g' => '',
      ],
    ],
    [
      'name' => 'catch_all#get',
      'postfix' => 'get',
      'url' => '/{a}/{b}/{c}/{d}/{e}/{f}/{g}',
      'verb' => 'GET',
      'defaults' => [
        'a' => '',
        'b' => '',
        'c' => '',
        'd' => '',
        'e' => '',
        'f' => '',
        'g' => '',
      ],
    ],
  ],
];
