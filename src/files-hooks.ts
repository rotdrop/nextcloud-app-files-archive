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
import { fileInfoToNode } from './toolkit/util/file-node-helper.ts';
import { emit, subscribe } from '@nextcloud/event-bus';
import type { NotificationEvent } from './toolkit/types/event-bus.d.ts';
import getInitialState from './toolkit/util/initial-state.ts';
import { DefaultType, registerFileAction, FileAction, Node, Permission } from '@nextcloud/files';
import { translate as t } from '@nextcloud/l10n';
import logger from './console.ts';
import logoSvg from '../img/app.svg?raw';
import type { InitialState } from './types/initial-state.d.ts';
import mount from './services/mount.ts';

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
  exec: mount,
  default: initialState?.mountByLeftClick ? DefaultType.DEFAULT : undefined,
  order: -1000,
}));
