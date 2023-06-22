/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023 Claus-Justus Heine
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

import Vue from 'vue';
import { appName } from './config.js';
import $ from './toolkit/util/jquery.js';
import generateAppUrl from './toolkit/util/generate-url.js';
import * as Ajax from './toolkit/util/ajax.js';
import { attachDialogHandlers } from './toolkit/util/dialogs.js';
import { getInitialState } from 'toolkit/services/InitialStateService.js';
import { generateFilePath, imagePath, generateUrl } from '@nextcloud/router';
import { showError, /* showSuccess, */ TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs';
import FilesTab from './views/FilesTab.vue';
import { Tooltip } from '@nextcloud/vue';

require('files-archive.scss');
require('dialogs.scss');

Vue.directive('tooltip', Tooltip);

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js');
Vue.mixin({ data() { return { appName }; }, methods: { t, n, generateUrl } });

const View = Vue.extend(FilesTab);
let TabInstance = null;

const initialState = getInitialState();

// a menu entry in order to mount archive files as "virtual" folders
const fileActionTemplate = {
  name: 'mount-archive',
  displayName: t(appName, 'Mount Archive'),
  altText: t(appName, 'Mount Archive'),
  // iconClass: 'icon-extract',
  // mime: 'httpd/unix-directory',
  // type: OCA.Files.FileActions.TYPE_DROPDOWN,
  // permissions: OC.PERMISSION_READ,
  // shouldRender(context) {}, is not invoked for TYPE_DROPDOWN
  icon() {
    return imagePath('core', 'actions/external');
  },
  // render(actionSpec, isDefault, context) {}, is not invoked for TYPE_DROPDOWN
  /**
   * Handle multi-page PDF download request. Stolen from the
   * files-app download action handler.
   *
   * @param {string} fileName TBD.
   * @param {object} context TBD.
   */
  actionHandler(fileName, context) {
    const fullPath = encodeURIComponent((context.dir === '/' ? '' : context.dir) + '/' + fileName);

    const mountUrl = initialState.mountBackgroundJob
      ? generateAppUrl('archive/schedule/mount/{fullPath}', { fullPath })
      : generateAppUrl('archive/mount/{fullPath}', { fullPath });

    // $file is a jQuery object, change that if the files-app gets overhauled
    const mountFileaction = context.$file.find('.fileactions .action-mount-archive');

    // don't allow a second click on the download action
    if (mountFileaction.hasClass('disabled')) {
      return;
    }

    const disableLoadingState = () => context.fileList.showFileBusyState(fileName, false);
    context.fileList.showFileBusyState(fileName, true);

    $.get(mountUrl)
      .fail((xhr, textStatus, errorThrown) => {
        if (xhr.status === 404) {
          Ajax.handleError(xhr, textStatus, errorThrown, disableLoadingState);
        } else {
          showError(t(appName, 'Unable to obtain mount status for archive file "{archivePath}".', { archivePath: fileName }), { timeout: TOAST_PERMANENT_TIMEOUT });
          disableLoadingState();
        }
      })
      .done((data) => {
        if (data.mounted) {
          const mountPointPath = data.mounts[0].mountPointPath;
          // make it relative
          showError(t(appName, 'The archive "{archivePath}" is already mounted on "{mountPointPath}".', { archivePath: fileName, mountPointPath }), { timeout: TOAST_PERMANENT_TIMEOUT });
          disableLoadingState();
          return;
        }
        console.info('DATA', data);
        $.post(mountUrl)
          .fail((xhr, textStatus, errorThrown) => {
            Ajax.handleError(xhr, textStatus, errorThrown, disableLoadingState);
          })
          .done((data) => {
            console.info('DATA', data);
            // const mountPointPath = data.mountPointPath;
            // showSuccess(t(appName, 'The archive "{archivePath}" has been mounted on "{mountPointPath}".', { archivePath: fileName, mountPointPath }));
            disableLoadingState();
            if (!initialState.mountBackgroundJob) {
              context.fileList.reload();
            }
          });
      });
  },
};

window.addEventListener('DOMContentLoaded', () => {

  attachDialogHandlers();

  console.info('INITIAL STATE', initialState);

  /**
   * Register a new tab in the sidebar
   */
  if (OCA.Files && OCA.Files.Sidebar) {
    OCA.Files.Sidebar.registerTab(new OCA.Files.Sidebar.Tab({
      id: appName,
      name: t(appName, 'Archive'),
      icon: 'icon-files-archive',

      enabled(fileInfo) {
        return initialState.archiveMimeTypes.indexOf(fileInfo.mimetype) >= 0;
      },

      async mount(el, fileInfo, context) {

        if (TabInstance) {
          TabInstance.$destroy();
        }

        TabInstance = new View({
          // Better integration with vue parent component
          parent: context,
        });

        // Only mount after we have all the info we need
        await TabInstance.update(fileInfo);

        TabInstance.$mount(el);
      },
      update(fileInfo) {
        console.info('ARGUMENTS', arguments);
        TabInstance.update(fileInfo);
      },
      destroy() {
        TabInstance.$destroy();
        TabInstance = null;
      },
    }));
  }

  if (OCA.Files && OCA.Files.fileActions) {
    const fileActions = OCA.Files.fileActions;

    fileActionTemplate.type = OCA.Files.FileActions.TYPE_DROPDOWN;
    fileActionTemplate.permissions = OC.PERMISSION_READ;
    for (const mimeType of initialState.archiveMimeTypes) {
      const fileAction = Object.assign({ mime: mimeType }, fileActionTemplate);
      fileActions.registerAction(fileAction);
    }
  }
});
