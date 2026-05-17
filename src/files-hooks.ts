/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022-2026 Claus-Justus Heine
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

import type { NotificationEvent } from './toolkit/types/event-bus.d.ts';
import type { InitialState } from './types/initial-state.d.ts';
import type { DestinationParameter } from './types/notification.d.ts';

import { emit, subscribe } from '@nextcloud/event-bus';
import {
  type ActionContext,

  DefaultType,
  Permission,
  registerFileAction,
} from '@nextcloud/files';
import { translate as t } from '@nextcloud/l10n';
import logoSvg from '../img/app.svg?raw';
import { appName } from './config.ts';
import logger from './console.ts';
import mount from './services/mount.ts';
import { fileInfoToNode } from './toolkit/util/file-node-helper.ts';
import getInitialState from './toolkit/util/initial-state.ts';

import './webpack-setup.ts';

const initialState = getInitialState<InitialState>();
const archiveMimeTypes: Array<string> = initialState?.archiveMimeTypes || [];

subscribe('notifications:notification:received', (event: NotificationEvent) => {
  logger.debug('FILES_ARCHIVE NOTIFICATION RECEIVED', { event });
  if (event?.notification?.app !== appName) {
    return;
  }
  const destinationData = event?.notification?.messageRichParameters?.destination as DestinationParameter;

  if (!destinationData?.status || !destinationData?.folder) {
    return;
  }
  try {
    const node = fileInfoToNode(JSON.parse(destinationData.folder));
    node.attributes['is-mount-root'] = true;

    logger.debug('FILES_ARCHIVE EMIT NODE CREATED', { node });

    emit('files:node:created', node);
  } catch (error) {
    logger.error('Error, unable to decode mount folder node', { error, event });
  }
});

registerFileAction({
  id: appName,
  displayName(_context) {
    return t(appName, 'Mount Archive');
  },
  title(_context: ActionContext) {
    return t(appName, 'Mount Archive');
  },
  iconSvgInline(_context: ActionContext) {
    return logoSvg;
  },
  enabled(context: ActionContext) {
    if (context.nodes.length !== 1) {
      return false;
    }
    const node = context.nodes[0];
    if (!(node.permissions & Permission.READ)) {
      return false;
    }
    return node.mime !== undefined && archiveMimeTypes.findIndex((mime) => mime === node.mime) >= 0;
  },
  exec: (context) => mount(context.nodes[0], context.view),
  default: initialState?.mountByLeftClick ? DefaultType.DEFAULT : undefined,
  order: -1000,
});
