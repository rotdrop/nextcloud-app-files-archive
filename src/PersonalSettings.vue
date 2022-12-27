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
    <AppSettingsSection :title="t(appName, 'Security Options')">
      <SettingsInputText
        v-model="humanArchiveSizeLimit"
        :label="t(appName, 'Archive Size Limit')"
        :hint="t(appName, 'Disallow archive extraction for archives with decompressed size larger than this limit.')"
        :disabled="loading"
        @update="saveTextInput(...arguments, 'archiveSizeLimit')"
      />
      <span v-if="archiveSizeLimitAdmin > 0" :class="{ hint: true, 'admin-limit-exceeded': archiveSizeLimitAdmin < archiveSizeLimit, 'icon-error': archiveSizeLimitAdmin < archiveSizeLimit }">
        {{ t(appName, 'Administrative size limit: {value}', { value: humanArchiveSizeLimitAdmin }) }}
      </span>
    </AppSettingsSection>
    <AppSettingsSection :title="t(appName, 'Mount Options')">
      <SettingsInputText
        v-model="mountPointTemplate"
        :label="t(appName, 'Template for the default name of the mount point')"
        :hint="t(appName, '{archiveFileName} will be replaced by the filename of the archive file without extensions.')"
        placeholder="{archiveFileName}"
        @update="saveTextInput(...arguments, 'mountPointTemplate')"
      />
      <div class="settings-option">
        <input :id="id + '-mount-strip-common-prefix'"
               v-model="mountStripCommonPathPrefixDefault"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('mountStripCommonPathPrefixDefault')"
        >
        <label :for="id + '-mount-strip-common-prefix'">
          {{ t(appName, 'strip common path prefix by default') }}
        </label>
      </div>
      <div class="settings-option">
        <input :id="id + '-mount-auto-rename'"
               v-model="mountPointAutoRename"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('mountPointAutoRename')"
        >
        <label :for="id + '-mount-auto-rename'">
          {{ t(appName, 'automatically change the mount point name if it already exists') }}
        </label>
      </div>
      <div class="settings-option">
        <input :id="id + '-mount-background-job'"
               v-model="mountBackgroundJob"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('mountBackgroundJob')"
        >
        <label :for="id + '-mount-background-job'">
          {{ t(appName, 'default to scheduling mount requests as background job') }}
        </label>
      </div>
    </AppSettingsSection>
    <AppSettingsSection :title="t(appName, 'Extraction Options')">
      <SettingsInputText
        v-model="extractTargetTemplate"
        :label="t(appName, 'Template for the default name of the extraction folder')"
        :hint="t(appName, '{archiveFileName} will be replaced by the filename of the archive file without extensions.')"
        placeholder="{archiveFileName}"
        @update="saveTextInput(...arguments, 'extractTargetTemplate')"
      />
      <div class="settings-option">
        <input :id="id + '-extract-strip-common-prefix'"
               v-model="extractStripCommonPathPrefixDefault"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('extractStripCommonPathPrefixDefault')"
        >
        <label :for="id + '-extract-strip-common-prefix'">
          {{ t(appName, 'strip common path prefix by default') }}
        </label>
      </div>
      <div class="settings-option">
        <input :id="id + '-extract-auto-rename'"
               v-model="extractTargetAutoRename"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('extractTargetAutoRename')"
        >
        <label :for="id + '-extract-auto-rename'">
          {{ t(appName, 'automatically change the target folder name if the target folder already exists') }}
        </label>
      </div>
      <div class="settings-option">
        <input :id="id + '-extract-background-job'"
               v-model="extractBackgroundJob"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('extractBackgroundJob')"
        >
        <label :for="id + '-extract-background-job'">
          {{ t(appName, 'default to scheduling extraction requests as background job') }}
        </label>
      </div>
    </AppSettingsSection>
  </SettingsSection>
</template>

<script>
import { appName } from './config.js'
import SettingsInputText from '@rotdrop/nextcloud-vue-components/lib/components/SettingsInputText'
import AppSettingsSection from '@nextcloud/vue/dist/Components/AppSettingsSection'
import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess, showInfo, TOAST_DEFAULT_TIMEOUT, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import settingsSync from './toolkit/mixins/settings-sync'

export default {
  name: 'PersonalSettings',
  components: {
    AppSettingsSection,
    SettingsSection,
    SettingsInputText,
  },
  data() {
    return {
      archiveSizeLimit: null,
      humanArchiveSizeLimit: '',
      archiveSizeLimitAdmin: null,
      humanArchiveSizeLimitAdmin: '',
      mountStripCommonPathPrefixDefault: false,
      mountPointTemplate: '{archiveFileName}',
      mountPointAutoRename: false,
      mountBackgroundJob: false,
      extractStripCommonPathPrefixDefault: false,
      extractTargetTemplate: '{archiveFileName}',
      extractTargetAutoRename: false,
      extractBackgroundJob: false,
      id: null,
      loading: true,
    }
  },
  mixins: [
    settingsSync,
  ],
  watch: {
  },
  created() {
    this.getData()
    this.id = this._uid
    console.info('UID', this._uid)
  },
  methods: {
    async getData() {
      // slurp in all personal settings
      this.fetchSettings('personal');
      this.loading = false
    },
    async saveTextInput(value, settingsKey, force) {
      return this.saveConfirmedSetting(value, 'personal', settingsKey, force);
    },
    async saveSetting(setting) {
      return this.saveSimpleSetting(setting, 'personal')
    },
  },
}
</script>
<style lang="scss" scoped>
.settings-section {
  :deep(.settings-section__title) {
    padding-left:60px;
    height:32px;
    position: relative;
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
    }
  }
  .app-settings-section {
    .settings-option {
      margin:0.5ex 0;
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
      &.admin-limit-exceeded {
        color:red;
        font-weight:bold;
        font-style:italic;
        &.icon-error {
          padding-left:20px;
          background-position:left;
        }
      }
    }
  }
}
</style>
<style lang="scss">
body[data-themes*="dark"] .settings-section__title::before {
  filter: Invert(); // avoid conflict with sass lower case invert()
}
</style>
