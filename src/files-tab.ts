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

import { appName } from './config.ts';
import Vue from 'vue';
import { createPinia, PiniaVuePlugin } from 'pinia';
import { Tooltip } from '@nextcloud/vue';
import FilesTab from './views/FilesTab.vue';
import type { LegacyFileInfo } from '@nextcloud/files';
import { translate as t, translatePlural as n } from '@nextcloud/l10n';

interface FilesTabVue extends Vue {
  update(fileInfo: LegacyFileInfo): Promise<unknown>,
}

Vue.mixin({ data() { return { appName } }, methods: { t, n } });
Vue.directive('tooltip', Tooltip);
Vue.use(PiniaVuePlugin);

const FilesTabVue = Vue.extend(FilesTab);
const pinia = createPinia();

const createTabInstance = (parent: Vue):FilesTabVue => new FilesTabVue({ parent, pinia })

export default createTabInstance;
