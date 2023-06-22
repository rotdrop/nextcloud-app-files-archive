<script>
/**
 * @copyright Copyright (c) 2022, 2023 Claus-Justus Heine <himself@claus-justus-heine.de>
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
</script>
<template>
  <SettingsSection :class="cloudVersionClasses"
                   :title="t(appName, 'Archive Manager, Admin Settings')"
  >
    <AppSettingsSection :title="t(appName, 'Archive Extraction')">
      <SettingsInputText
        v-model="humanArchiveSizeLimit"
        :label="t(appName, 'Archive Size Limit')"
        :hint="t(appName, 'Disallow archive extraction for archives with decompressed size larger than this limit.')"
        :disabled="loading"
        @update="saveTextInput(...arguments, 'archiveSizeLimit')"
      />
    </AppSettingsSection>
  </SettingsSection>
</template>

<script>
import { appName } from './config.js'
import SettingsSection from '@nextcloud/vue/dist/Components/NcSettingsSection'
import AppSettingsSection from '@nextcloud/vue/dist/Components/NcAppSettingsSection'
import ListItem from '@rotdrop/nextcloud-vue-components/lib/components/ListItem'
import SettingsInputText from '@rotdrop/nextcloud-vue-components/lib/components/SettingsInputText'
import axios from '@nextcloud/axios'
import { showError, showSuccess, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import { generateUrl } from '@nextcloud/router'
import settingsSync from './toolkit/mixins/settings-sync'
import cloudVersionClasses from './toolkit/util/cloud-version-classes.js'

export default {
  name: 'AdminSettings',
  components: {
    AppSettingsSection,
    ListItem,
    SettingsSection,
    SettingsInputText,
  },
  data() {
    return {
      cloudVersionClasses,
      archiveSizeLimit: null,
      humanArchiveSizeLimit: null,
      loading: true,
    }
  },
  mixins: [
    settingsSync,
  ],
  created() {
    this.getData()
  },
  computed: {
  },
  watch: {
  },
  methods: {
    async getData() {
      // slurp in all settings
      this.fetchSettings('admin');
      this.loading = false
    },
    async saveTextInput(value, settingsKey, force) {
      await this.saveConfirmedSetting(value, 'admin', settingsKey, force)
    },
    async saveSetting(setting) {
      return this.saveSimpleSetting(setting, 'admin')
    },
  },
}
</script>
<style lang="scss" scoped>
.cloud-version {
  --cloud-theme-filter: none;
  &.cloud-version-major-25 {
    --cloud-theme-filter: var(--background-invert-if-dark);
  }
}
.settings-section {
  .flex-container {
    display:flex;
    &.flex-center {
      align-items:center;
    }
  }
  :deep() &__title {
    padding-left:60px;
    position:relative;
    height:32px;
    &::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 32px;
      height: 32px;
      background-size:32px;
      background-image:url('../img/app-dark.svg');
      background-repeat:no-repeat;
      background-origin:border-box;
      background-position:left center;
      filter: var(--cloud-theme-filter);
    }
  }
  :deep(.app-settings-section) {
    margin-bottom: 40px;
  }
}
</style>
