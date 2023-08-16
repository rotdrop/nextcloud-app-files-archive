<script>
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
</script>
<template>
  <FilePrefixPicker v-bind="$attrs"
                    :file-picker-title="filePickerTitle"
                    :only-dir-name="onlyDirName"
                    v-on="$listeners"
                    @error:invalidDirName="showDirNameInvalid"
                    @update:dirName="showDirNameUpdated"
  />
</template>
<script>
import { appName } from '../config.js'
import { showError, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import FilePrefixPicker from '@rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker'

export default {
  name: 'FilePrefixPickerWrapper',
  components: {
    FilePrefixPicker,
  },
  inheritAttrs: false,
  props: {
    filePickerTitle: {
      type: String,
      default: t(appName, 'Choose a prefix-folder'),
    },
    onlyDirName: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    showDirNameUpdated(dir, base) {
      if (!this.onlyDirName) {
        showInfo(t(appName, 'Selected path: "{dir}/{base}/".', { dir, base }))
      }
    },
    showDirNameInvalid(dir) {
      showError(t(appName, 'Invalid path selected: "{dir}".', { dir }), { timeout: TOAST_PERMANENT_TIMEOUT })
    },
  },
}
</script>
