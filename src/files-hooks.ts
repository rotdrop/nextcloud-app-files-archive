/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023, 2024, 2025 Claus-Justus Heine
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

import { appName } from './config.ts';
import generateAppUrl from './toolkit/util/generate-url.ts';
import { fileInfoToNode } from './toolkit/util/file-node-helper.ts';
import { emit, subscribe } from '@nextcloud/event-bus';
import axios from '@nextcloud/axios';
import type { NotificationEvent } from './toolkit/types/event-bus.d.ts';
import getInitialState from './toolkit/util/initial-state.ts';
import { showError, showSuccess, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs';
import { registerFileAction, FileAction, Node, Permission } from '@nextcloud/files';
import { translate as t } from '@nextcloud/l10n';
import { isAxiosErrorResponse } from './toolkit/types/axios-type-guards.ts';
import logger from './console.ts';
import logoSvg from '../img/app.svg?raw';
import type { InitialState } from './types/initial-state.d.ts';
import type { ArchiveMount, GetArchiveMountResponse } from './model/archive-mount';

require('./webpack-setup.ts');

const initialState = getInitialState<InitialState>();
const archiveMimeTypes: Array<string> = initialState?.archiveMimeTypes || [];

subscribe('notifications:notification:received', (event: NotificationEvent) => {
  logger.debug('FILES_ARCHIVE NOTIFICATION RECEIVED', { event });
  if (event?.notification?.app !== appName) {
    return;
  }
  const successData = event.notification?.messageRichParameters;
  if (!successData?.destination?.status || !successData?.destination?.folder) {
    return;
  }
  try {
    const node = fileInfoToNode(JSON.parse(successData.destination.folder));
    node.attributes['is-mount-root'] = true;

    logger.debug('FILES_ARCHIVE EMIT NODE CREATED', { node });

    emit('files:node:created', node);
  } catch (error) {
    logger.error('Error, unable to decode mount folder node', { event });
  }
});

registerFileAction(new FileAction({
  id: appName,
  displayName(/* nodes: Node[], view: View */) {
    return t(appName, 'Mount Archive');
  },
  title(/* files: Node[], view: View */) {
    return t(appName, 'Mount Archive');
  },
  iconSvgInline(/* files: Node[], view: View) */) {
    return logoSvg;
  },
  enabled(nodes: Node[]/* , view: View) */) {
    if (nodes.length !== 1) {
      return false;
    }
    const node = nodes[0];
    if (!(node.permissions & Permission.READ)) {
      return false;
    }
    return node.mime !== undefined && archiveMimeTypes.findIndex((mime) => mime === node.mime) >= 0;
  },
  async exec(node: Node/* , view: View, dir: string */) {

    const fullPath = encodeURIComponent(node.path);

    const mountStatusUrl = generateAppUrl('archive/mount/{fullPath}', { fullPath }, undefined);

    const mountUrl = initialState?.mountBackgroundJob
      ? generateAppUrl('archive/schedule/mount/{fullPath}', { fullPath }, undefined)
      : mountStatusUrl;

    try {
      const response = await axios.get<GetArchiveMountResponse>(mountStatusUrl);
      const data = response.data;
      if (data.mounted) {
        const mountPointPath = data.mounts[0].mountPointPath;
        // make it relative
        showError(t(appName, 'The archive "{archivePath}" is already mounted on "{mountPointPath}".', { archivePath: node.path, mountPointPath }), { timeout: TOAST_PERMANENT_TIMEOUT });
        return null;
      }
      logger.info('DATA', data);
      try {
        const response = await axios.post<ArchiveMount>(mountUrl);
        const data = response.data;
        logger.info('DATA', data);
        const mountPointPath = data.mountPointPath;
        if (!initialState?.mountBackgroundJob) {
          showSuccess(t(appName, 'The archive "{archivePath}" has been mounted on "{mountPointPath}".', {
            archivePath: node.path,
            mountPointPath,
          }));
          const mountNode = fileInfoToNode(data.mountPoint);
          mountNode.attributes['is-mount-root'] = true;
          logger.info('MOUNT NODE', mountNode);

          // Update files list
          emit('files:node:created', mountNode);
        }
      } catch (e) {
        logger.error('ERROR', e);
        if (isAxiosErrorResponse(e)) {
          const messages: string[] = [];
          if (e.response.data) {
            const responseData = e.response.data as { messages?: string[] };
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push(t(appName, 'Mount request failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText,
            }));
          }
          for (const message of messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT });
          }
        }
      }
    } catch (e) {
      logger.error('ERROR', e);
      if (isAxiosErrorResponse(e)) {
        const messages: string[] = [];
        if (e.response.data) {
          const responseData = e.response.data as { messages?: string[] };
          if (responseData.messages) {
            messages.splice(messages.length, 0, ...responseData.messages);
          }
        }
        if (!messages.length) {
          messages.push(t(appName, 'Unable to obtain mount status for archive file "{archivePath}".', {
            archivePath: node.path,
          }));
        }
        for (const message of messages) {
          showError(message, { timeout: TOAST_PERMANENT_TIMEOUT });
        }
      }
    }
    return null;
  },
}));
