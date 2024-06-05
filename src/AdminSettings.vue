<!--
 - @copyright Copyright (c) 2022-2024 Claus-Justus Heine <himself@claus-justus-heine.de>
 -
 - @author Claus-Justus Heine <himself@claus-justus-heine.de>
 -
 - @license AGPL-3.0-or-later
 -
 - This program is free software: you can redistribute it and/or modify
 - it under the terms of the GNU Affero General Public License as
 - published by the Free Software Foundation, either version 3 of the
 - License, or (at your option) any later version.
 -
 - This program is distributed in the hope that it will be useful,
 - but WITHOUT ANY WARRANTY; without even the implied warranty of
 - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 - GNU Affero General Public License for more details.
 -
 - You should have received a copy of the GNU Affero General Public License
 - along with this program. If not, see <http://www.gnu.org/licenses/>.
 -->
<template>
  <div :class="['templateroot', ...cloudVersionClasses]">
    <h1 class="title">
      {{ t(appName, 'Archive Manager, Admin Settings') }}
    </h1>
    <NcSettingsSection :name="t(appName, 'Archive Extraction')">
      <TextField :value.sync="humanArchiveSizeLimit"
                 :label="t(appName, 'Archive Size Limit')"
                 :hint="t(appName, 'Disallow archive extraction for archives with decompressed size larger than this limit.')"
                 :disabled="loading"
                 @submit="saveTextInput('archiveSizeLimit', humanArchiveSizeLimit)"
      />
    </NcSettingsSection>
    <NcSettingsSection :name="t(appName, 'Diagnostics')">
      <h3>{{ t(appName, "Archive Formats") }}</h3>
      <!-- eslint-disable-next-line vue/no-v-html -->
      <pre :class="{ loading: diagnostics.formats === null, diagnostics: true }" v-html="diagnostics.formats" />
      <h3>{{ t(appName, "Supported Drivers") }}</h3>
      <!-- eslint-disable-next-line vue/no-v-html -->
      <pre :class="{ loading: diagnostics.drivers === null, diagnostics: true }" v-html="diagnostics.drivers" />
    </NcSettingsSection>
  </div>
</template>

<script>
import {
  NcSettingsSection,
} from '@nextcloud/vue'
import axios from '@nextcloud/axios'
import { showError /* , showSuccess, showInfo, TOAST_PERMANENT_TIMEOUT */ } from '@nextcloud/dialogs'
import { appName } from './config.js'
import generateUrl from './toolkit/util/generate-url.js'
import TextField from '@rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue'
import settingsSync from './toolkit/mixins/settings-sync.js'
import cloudVersionClasses from './toolkit/util/cloud-version-classes.js'

export default {
  name: 'AdminSettings',
  components: {
    NcSettingsSection,
    TextField,
  },
  mixins: [
    settingsSync,
  ],
  data() {
    return {
      cloudVersionClasses,
      archiveSizeLimit: null,
      humanArchiveSizeLimit: null,
      loading: true,
      diagnostics: {
        formats: null,
        drivers: null,
      },
    }
  },
  created() {
    this.getData()
  },
  methods: {
    async getData() {
      // slurp in all settings
      this.fetchSettings('admin')
      this.loading = false
      this.getFormatsMatrix()
      this.getDriversStatus()
    },
    async saveTextInput(settingsKey, value, force) {
      if (value === undefined) {
        value = this[settingsKey] || ''
      }
      await this.saveConfirmedSetting(value, 'admin', settingsKey, force)
    },
    async saveSetting(setting) {
      return this.saveSimpleSetting(setting, 'admin')
    },
    async getFormatsMatrix() {
      try {
        const response = await await axios.get(generateUrl('diagnostics/archive/formats', {}))
        this.diagnostics.formats = response?.data?.html || null
        if (!this.diagnostics.formats) {
          console.error('UNEXPECTED RESPONSE', response)
        }
        return
      } catch (e) {
        console.error('ERROR', e)
      }
      showError(t(appName, 'Unable to query the archive-format support matrix.'))
    },
    async getDriversStatus() {
      try {
        const response = await await axios.get(generateUrl('diagnostics/archive/drivers', {}))
        this.diagnostics.drivers = response?.data?.html || null
        if (!this.diagnostics.formats) {
          console.error('UNEXPECTED RESPONSE', response)
        }
        return
      } catch (e) {
        console.error('ERROR', e)
      }
      showError(t(appName, 'Unable to query the information about the available archive backend drivers.'))
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
.templateroot::v-deep {
  h1.title {
    margin: 30px 30px 0px;
    font-size:revert;
    font-weight:revert;
    position: relative;
    padding-left:60px;
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
  .flex-container {
    display:flex;
    &.flex-center {
      align-items:center;
    }
  }
  .diagnostics {
    min-height: 2ex;
    font-family: monospace;
    font-size: 80%;
    line-height: 100%;
    max-width: 100%;
    overflow-x: auto;
  }
}
</style>
