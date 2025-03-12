/**
 * @copyright Copyright (c) 2022, 2023, 2025 Claus-Justus Heine <himself@claus-justus-heine.de>
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 *
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
 *
 */

import { appName } from './config.ts';
import { generateFilePath } from '@nextcloud/router';

import Vue from 'vue';
import PersonalSettings from './PersonalSettings.vue';

import { Tooltip } from '@nextcloud/vue';

Vue.directive('tooltip', Tooltip);

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js/');

Vue.mixin({ data() { return { appName }; }, methods: { t, n } });

export default new Vue({
  el: '#personal-settings',
  render: h => h(PersonalSettings),
});
