/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine
 * @license AGPL-3.0-or-later
 *
 * Loosely based on files_external/js/mountsfilelist.js
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

import { appName, appLongName } from './config.js';
import axios from '@nextcloud/axios';
import { generateUrl, imagePath } from '@nextcloud/router';
import jQuery from './toolkit/util/jquery.js';

const $ = jQuery;

/**
 * @augments OCA.Files.FileList
 *
 * @classdesc Archive mounts file list.
 *
 * Displays a list of mounted archive files visible
 * for the current user.
 *
 * @param {jQuery} $el container element with existing markup for the #controls
 * and a table
 * @param {object} options map of options, see other parameters
 */
const FileList = function($el, options) {
  this.initialize($el, options);
};

FileList.prototype = Object.assign(
  {},
  OCA.Files.FileList.prototype,
  /** @lends OCA.FilesArchive.FileList.prototype */ {
    appName: appLongName,

    _allowSelection: false,

    initialize($el, options) {
      OCA.Files.FileList.prototype.initialize.apply(this, arguments);
    },

    /**
     * @param {OCA.FilesArchive.MountPointInfo} fileData TBD.
     */
    _createRow(fileData) {
      // TODO: hook earlier and render the whole row here
      const $tr = OCA.Files.FileList.prototype._createRow.apply(this, arguments);
      const $scopeColumn = $('<td class="column-scope column-last"><span></span></td>');
      const $backendColumn = $('<td class="column-backend"></td>');
      let scopeText = t('files_external', 'Personal');
      if (fileData.scope === 'system') {
        scopeText = t('files_external', 'System');
      }
      $tr.find('.filesize,.date').remove();
      $scopeColumn.find('span').text(scopeText);
      $backendColumn.text(fileData.backend);
      $tr.find('td.filename').after($scopeColumn).after($backendColumn);
      return $tr;
    },

    updateEmptyContent() {
      const dir = this.getCurrentDirectory();
      if (dir === '/') {
        // root has special permissions
        this.$el.find('#emptycontent').toggleClass('hidden', !this.isEmpty);
        this.$el.find('#filestable thead th').toggleClass('hidden', this.isEmpty);
      } else {
        OCA.Files.FileList.prototype.updateEmptyContent.apply(this, arguments);
      }
    },

    getDirectoryPermissions() {
      return OC.PERMISSION_READ | OC.PERMISSION_DELETE;
    },

    updateStorageStatistics() {
      // no op because it doesn't have
      // storage info like free space / used space
    },

    reload() {
      this.showMask();
      if (this._reloadAbort) {
        this._reloadAbort.abort();
      }

      // there is only root
      this._setCurrentDir('/', false);

      const callBack = this.reloadCallback.bind(this);
      const url = generateUrl('/apps/' + appName + '/archive/mount');
      this._reloadAbort = new AbortController();

      return axios.get(url, {
        signal: this._reloadAbort,
      }).then(callBack, callBack);
    },

    reloadCallback(result) {
      delete this._reloadAbort;
      this.hideMask();

      console.info('RESULT FROM AXIOS CALL', result);

      // if (result.ocs && result.ocs.data) {
      //   this.setFiles(this._makeFiles(result.ocs.data));
      //   return true;
      // }
      return false;
    },

    /**
     * Converts the OCS API  response data to a file info
     * list
     *
     * @param {Array} data OCS API mounts array.
     * @return {Array} of file info maps.
     */
    _makeFiles(data) {
      const files = data.map(function(fileData) {
        fileData.icon = imagePath('core', 'filetypes/folder-external');
        fileData.mountType = 'external';
        return fileData;
      });

      files.sort(this._sortComparator);

      return files;
    },
  });

/**
 * Mount point info attributes.
 *
 * @typedef {object} OCA.FilesArchive.MountPointInfo
 *
 * @property {string} name mount point name
 * @property {string} scope mount point scope "personal" or "system"
 * @property {string} backend external storage backend name
 */

export default FileList;
