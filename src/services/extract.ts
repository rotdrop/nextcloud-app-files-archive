/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @author Fabio Fantoni <fabio.fantoni@m2r.biz>
 * @copyright 2025, 2026 Claus-Justus Heine
 * @copyright 2026 Fabio Fantoni
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

import type { INode } from '@nextcloud/files';
import type { FileInfoDTO } from '../toolkit/util/file-node-helper.ts';
import type { InitialState } from '../types/initial-state.d.ts';

import axios from '@nextcloud/axios';
import { showError, showSuccess, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs';
import { emit } from '@nextcloud/event-bus';
import { NodeStatus } from '@nextcloud/files';
import { translate as t } from '@nextcloud/l10n';
import { appName } from '../config.ts';
import logger from '../console.ts';
import { isAxiosErrorResponse } from '../toolkit/types/axios-type-guards.ts';
import { fileInfoToNode } from '../toolkit/util/file-node-helper.ts';
import generateAppUrl from '../toolkit/util/generate-url.ts';
import getInitialState from '../toolkit/util/initial-state.ts';

const initialState = getInitialState<InitialState>();

interface ExtractResponse {
  archivePath: string;
  targetFileId: number;
  targetPath: string;
  targetFolder: FileInfoDTO;
  messages: string[];
}

/**
 * Extract the given archive file to a fresh folder in its parent
 * directory. The folder name is determined by the server from the
 * configured extraction folder template.
 *
 * @param node The file-system node referring to the archive file.
 *
 * @return null, status feedback is provided by toasts.
 */
const extract = async (node: INode) => {
  const savedNodeStatus = node.status;

  const encodedPath = encodeURIComponent(node.path);

  try {
    if (initialState?.extractBackgroundJob) {
      const extractUrl = generateAppUrl('archive/schedule/extract/{encodedPath}', { encodedPath }, undefined);
      const response = await axios.post<{ targetPath: string; jobType: string; messages: string[] }>(extractUrl);
      const targetPath = response.data.targetPath;
      showSuccess(t(appName, 'The archive "{archivePath}" will be extracted asynchronously to "{targetPath}", you will be notified on completion.', {
        archivePath: node.path,
        targetPath,
      }));
    } else {
      node.status = NodeStatus.LOADING;
      emit('files:node:updated', node);
      const extractUrl = generateAppUrl('archive/extract/{encodedPath}', { encodedPath }, undefined);
      const response = await axios.post<ExtractResponse>(extractUrl, {
        stripCommonPathPrefix: !!initialState?.extractStripCommonPathPrefixDefault,
      });
      const data = response.data;
      logger.info('DATA', data);
      showSuccess(t(appName, 'The archive "{archivePath}" has been extracted to "{targetPath}".', {
        archivePath: node.path,
        targetPath: data.targetPath,
      }));
      const targetNode = fileInfoToNode(data.targetFolder);
      logger.info('TARGET NODE', targetNode);

      // Update files list
      emit('files:node:created', targetNode);

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
        messages.push(t(appName, 'Extraction request failed with error {status}, "{statusText}".', {
          status: e.response.status,
          statusText: e.response.statusText,
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

export default extract;
