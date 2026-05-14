/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022, 2023, 2024, 2025, 2026 Claus-Justus Heine
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

import type { InitialState } from './types/initial-state.d.ts';

import { registerSidebarTab } from '@nextcloud/files';
import { translate as t } from '@nextcloud/l10n';
import { defineAsyncComponent, defineCustomElement } from 'vue';
import logoSvg from '../img/app.svg?raw';
import { appName } from './config.ts';
import getInitialState from './toolkit/util/initial-state.ts';

const sidebarTabTag = `${appName}-files-sidebar-tab` as const;

if (window.customElements.get(sidebarTabTag) === undefined) {
  window.customElements.define(
    sidebarTabTag,
    defineCustomElement(defineAsyncComponent(() => import('./views/FilesTab.vue')), { shadowRoot: false }),
  );

  const initialState = getInitialState<InitialState>();

  registerSidebarTab({
    id: appName,
    order: 50,
    displayName: t(appName, 'Archive'),
    iconSvgInline: logoSvg,
    tagName: sidebarTabTag,
    enabled: (context) => !!initialState && initialState.archiveMimeTypes.indexOf(context.node.mime) >= 0,
  });
}
