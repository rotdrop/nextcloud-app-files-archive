/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2025 Claus-Justus Heine
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

import axios from '@nextcloud/axios';
import generateAppUrl from '../toolkit/util/generate-url.ts';
import getInitialState from '../toolkit/util/initial-state.ts';
import logger from '../console.ts';
import type { ArchiveMount, GetArchiveMountResponse } from '../model/archive-mount';
import type { InitialState } from '../types/initial-state.d.ts';
import { Node, NodeStatus, View } from '@nextcloud/files';
import { appName } from '../config.ts';
import { emit } from '@nextcloud/event-bus';
import { fileInfoToNode } from '../toolkit/util/file-node-helper.ts';
import { isAxiosErrorResponse } from '../toolkit/types/axios-type-guards.ts';
import { showError, showSuccess, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs';
import { translate as t } from '@nextcloud/l10n';

const initialState = getInitialState<InitialState>();

const openMountPoint = async (mountNode: Node, view: View) => {
  // maybe also navigate to the folder (of course only on synchronous mount requests)
  try {
    await OCP.Files.Router.goToRoute(
      null,
      { view: view.id, fileid: String(mountNode.fileid) },
      { dir: mountNode.path },
    );
  } catch (error) {
    logger.error(error);
    showError(t(appName, 'Mounting the archive was seemingly successful, but navigating to the mount point failed: "{error}".', { error }));
  }
};

const mount = async (node: Node, view: View) => {
  const savedNodeStatus = node.status;

  const encodedPath = encodeURIComponent(node.path);

  const mountStatusUrl = generateAppUrl('archive/mount/{encodedPath}', { encodedPath }, undefined);

  try {
    const response = await axios.get<GetArchiveMountResponse>(mountStatusUrl);
    const data = response.data;
    if (data.mounted) {
      node.status = NodeStatus.LOADING;
      emit('files:node:updated', node);
      const mount = data.mounts[0];
      const mountNode = fileInfoToNode(mount.mountPoint);
      const mountPointPath = mount.mountPointPath;
      // make it relative
      showError(t(appName, 'The archive "{archivePath}" is already mounted on "{mountPointPath}".', { archivePath: node.path, mountPointPath }), { timeout: TOAST_PERMANENT_TIMEOUT });
      await openMountPoint(mountNode, view);
      node.status = savedNodeStatus;
      emit('files:node:updated', node);
      return null;
    }
    try {
      if (initialState?.mountBackgroundJob) {
        const mountUrl = generateAppUrl('archive/schedule/mount/{encodedPath}', { encodedPath }, undefined);
        const response = await axios.post<{ targetPath: string, jobType: string, messages: string[] }>(mountUrl);
        const data = response.data;
        logger.info('DATA', data);
        const mountPointPath = data.targetPath;
        showSuccess(t(appName, 'The archive "{archivePath}" will be mounted asynchronously on "{mountPointPath}", you will be notified on completion.', {
          archivePath: node.path,
          mountPointPath,
        }));
      } else {
        node.status = NodeStatus.LOADING;
        emit('files:node:updated', node);
        const mountUrl = mountStatusUrl;
        const response = await axios.post<ArchiveMount>(mountUrl);
        const data = response.data;
        logger.info('DATA', data);
        const mountPointPath = data.mountPointPath;
        showSuccess(t(appName, 'The archive "{archivePath}" has been mounted on "{mountPointPath}".', {
          archivePath: node.path,
          mountPointPath,
        }));
        const mountNode = fileInfoToNode(data.mountPoint);
        mountNode.attributes['is-mount-root'] = true;
        logger.info('MOUNT NODE', mountNode);

        // Update files list
        emit('files:node:created', mountNode);

        // maybe also navigate to the folder (of course only on synchronous mount requests)
        await openMountPoint(mountNode, view);

        node.status = undefined;
        emit('files:node:updated', node);
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
  node.status = savedNodeStatus;
  emit('files:node:updated', node);
  return null;
};

export default mount;
