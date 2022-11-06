<script>
/**
 * @copyright Copyright (c) 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
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
  <SettingsSection :class="appName" :title="t(appName, 'Archive Manager, Personal Settings')">
    <AppSettingsSection :title="t(appName, 'Archive Extraction')">
      <SettingsInputText
        v-model="archiveSizeLimit"
        :label="t(appName, 'Archive Size Limit')"
        :hint="t(appName, 'Disallow archive extraction for archives with decompressed size larger than this limit.')"
        :disabled="loading"
        @update="saveTextInput(...arguments, 'archiveSizeLimit')"
      />
      <span v-if="archiveSizeLimitAdmin > 0" class="hint">
        {{ t(appName, 'Administrative size limit: {archiveSizeLimit}', admin) }}
      </span>
    </AppSettingsSection>
  </SettingsSection>
</template>

<script>
import { appName } from './config.js'
import AppSettingsSection from '@nextcloud/vue/dist/Components/AppSettingsSection'
import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'
import SettingsInputText from './components/SettingsInputText'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess, showInfo, TOAST_DEFAULT_TIMEOUT, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import settingsSync from './mixins/settings-sync'

export default {
  name: 'PersonalSettings',
  components: {
    AppSettingsSection,
    SettingsSection,
    SettingsInputText,
  },
  data() {
    return {
      loading: true,
      archiveSizeLimit: '',
      archiveSizeLimitAdmin: '',
    }
  },
  mixins: [
    settingsSync,
  ],
  watch: {
  },
  created() {
    this.getData()
  },
  methods: {
    async getData() {
      // slurp in all personal settings
      this.fetchSettings('personal');
      this.loading = false
    },
    async saveTextInput(value, settingsKey, force) {
      this.saveConfirmedSetting(value, 'personal', settingsKey, force);
    },
    async saveSetting(setting) {
      this.saveSimpleSetting(setting, 'personal')
    },
  },
}
</script>
<style lang="scss" scoped>
.settings-section {
  :deep(.settings-section__title) {
    padding-left:60px;
    background-image:url('../img/app-dark.svg');
    background-repeat:no-repeat;
    background-origin:border-box;
    background-size:32px;
    background-position:left center;
    height:32px;
  }
  .flex-container {
    display:flex;
    &.flex-center {
      align-items:center;
    }
  }
  .label-container {
    height:34px;
    display:flex;
    align-items:center;
    justify-content:left;
  }
  .hint {
    color: var(--color-text-lighter);
    font-size: 80%;
  }
}
</style>
