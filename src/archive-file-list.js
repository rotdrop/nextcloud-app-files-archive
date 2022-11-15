/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine
 * @license AGPL-3.0-or-later
 *
 * Loosely based on files_external/js/app.js
 * Copyright (c) 2014 Vincent Petry <pvince81@owncloud.com>
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

import $ from './toolkit/util/jquery.js';
import { joinPaths } from '@nextcloud/paths';
import ArchiveFileList from './ArchiveFileList.js';

let fileList = null;

const initList = function($el) {
  if (fileList) {
    return fileList;
  }

  fileList = new ArchiveFileList(
    $el, {
      fileActions: createFileActions(),
    }
  );

  extendFileList(fileList);
  this.fileList.appName = t('files_external', 'External storage');
  return this.fileList;
};

const removeList = function() {
  if (this.fileList) {
    this.fileList.$fileList.empty();
  }
};

const createFileActions = function() {
  // inherit file actions from the files app
  const fileActions = new OCA.Files.FileActions();
  fileActions.registerDefaultActions();

  // when the user clicks on a folder, redirect to the corresponding
  // folder in the files app instead of opening it directly
  fileActions.register('dir', 'Open', OC.PERMISSION_READ, '', function(filename, context) {
    OCA.Files.App.setActiveView('files', { silent: true });
    OCA.Files.App.fileList.changeDirectory(
      joinPaths(context.$file.attr('data-path'), filename), true, true);
  });
  fileActions.setDefault('dir', 'Open');
  return fileActions;
};

const extendFileList = function(fileList) {
  // remove size column from summary
  fileList.fileSummary.$el.find('.filesize').remove();
};

window.addEventListener('DOMContentLoaded', () => {

  $('#app-content-archivemounts').on('show', function(e) {
    initList($(e.target));
  });
  $('#app-content-archivemounts').on('hide', function() {
    removeList();
  });

  /* Status Manager ?? */
  if ($('#filesApp').val()) {

    $('#app-content-files')
      .add('#app-content-archivemounts')
      .on('changeDirectory', function(e) {
        console.info('CHANGE DIRECTORY');
        // if (e.dir === '/') {
        //   constr mount_point = e.previousDir.split('/', 2)[1];
        //   // Every time that we return to / root folder from a mountpoint, mount_point status is rechecked
        //   OCA.Files_External.StatusManager.getMountPointList(function() {
        //     OCA.Files_External.StatusManager.recheckConnectivityForMount([mount_point], true);
        //   });
        // }
      })
      .on('fileActionsReady', function(e) {
        console.info('FILE ACTIONS READY');
        // if ($.isArray(e.$files)) {
        //   if (OCA.Files_External.StatusManager.mountStatus === null ||
        //       OCA.Files_External.StatusManager.mountPointList === null ||
        //       _.size(OCA.Files_External.StatusManager.mountStatus) !== _.size(OCA.Files_External.StatusManager.mountPointList)) {
        //     // Will be the very first check when the files view will be loaded
        //     OCA.Files_External.StatusManager.launchFullConnectivityCheckOneByOne();
        //   } else {
        //     // When we change between general files view and external files view
        //     OCA.Files_External.StatusManager.getMountPointList(function(){
        //       var fileNames = [];
        //       $.each(e.$files, function(key, value){
        //         fileNames.push(value.attr('data-file'));
        //       });
        //       // Recheck if launched but work from cache
        //       OCA.Files_External.StatusManager.recheckConnectivityForMount(fileNames, false);
        //     });
        //   }
        // }
      });
  }
  /* End Status Manager */

});
