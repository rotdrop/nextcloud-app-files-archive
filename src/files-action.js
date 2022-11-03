/**
 * @author Claus-Justus Heine
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

import { appName } from './config.js';
import $ from './util/jquery.js';
import generateUrl from './util/generate-url.js';
import * as Ajax from './util/ajax.js';
import { attachDialogHandlers } from './util/dialogs.js';
import { getInitialState } from 'services/InitialStateService.js';

require('dialogs.scss');

const initialState = getInitialState();

// a menu entry in order to mount archive files as "virtual" folders
const fileActionTemplate = {
  name: 'mount-archive',
  displayName: t(appName, '(Un-)Mount Archive'),
  altText: t(appName, '(Un-)Mount Archive'),
  iconClass: 'icon-extract',
  // mime: 'httpd/unix-directory',
  // type: OCA.Files.FileActions.TYPE_DROPDOWN,
  // permissions: OC.PERMISSION_READ,
  // shouldRender(context) {}, is not invoked for TYPE_DROPDOWN
  // icon() {
  //   return imagePath('core', 'filetypes/application-pdf');
  // },
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

    const mountUrl = generateUrl('archive/mount/{fullPath}', { fullPath });
    const unmountUrl = generateUrl('archive/unmount/{fullPath}', { fullPath });

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
          console.info('ERROR', xhr, textStatus, errorThrown);
          Ajax.handleError(xhr, textStatus, errorThrown, disableLoadingState);
        }
      })
      .done((data) => {
        console.info('DATA', data);
        const url = data.mounted ? unmountUrl : mountUrl;
        $.post(url)
          .fail((xhr, textStatus, errorThrown) => {
            Ajax.handleError(xhr, textStatus, errorThrown, disableLoadingState);
          })
          .done((data) => {
            console.info('DONE');
            disableLoadingState();
          });
      });
  },
};

window.addEventListener('DOMContentLoaded', () => {

  attachDialogHandlers();

  console.info('INITIAL STATE', initialState);

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
