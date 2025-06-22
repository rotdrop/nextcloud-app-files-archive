<!--
 - @copyright Copyright (c) 2022-2025 Claus-Justus Heine <himself@claus-justus-heine.de>
 - @author Claus-Justus Heine <himself@claus-justus-heine.de>
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
  <div :class="['templateroot', appName, ...cloudVersionClasses]">
    <h1 class="title">
      {{ t(appName, 'Archive Manager, Personal Settings') }}
    </h1>
    <NcSettingsSection :name="t(appName, 'Security Options')">
      <TextField :value.sync="settings.humanArchiveSizeLimit"
                 :label="t(appName, 'Archive Size Limit')"
                 :hint="t(appName, 'Disallow archive extraction for archives with decompressed size larger than this limit.')"
                 :disabled="loading"
                 @submit="saveTextInput('archiveSizeLimit', settings.humanArchiveSizeLimit)"
      />
      <span v-if="isLt(0, settings.archiveSizeLimitAdmin)"
            :class="{
              hint: true,
              'admin-limit-exceeded': isLt(settings.archiveSizeLimitAdmin, settings.archiveSizeLimit),
              'icon-error': isLt(settings.archiveSizeLimitAdmin, settings.archiveSizeLimit),
            }"
      >
        {{ t(appName, 'Administrative size limit: {value}', { value: settings.humanArchiveSizeLimitAdmin }) }}
      </span>
    </NcSettingsSection>
    <NcSettingsSection :name="t(appName, 'Mount Options')">
      <TextField :value.sync="settings.mountPointTemplate"
                 :label="t(appName, 'Template for the default name of the mount point')"
                 :hint="t(appName, '{archiveFileName} will be replaced by the filename of the archive file without extensions.')"
                 placeholder="{archiveFileName}"
                 @submit="saveTextInput('mountPointTemplate')"
      />
      <div class="settings-option">
        <input :id="id + '-mount-strip-common-prefix'"
               v-model="settings.mountStripCommonPathPrefixDefault"
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
               v-model="settings.mountPointAutoRename"
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
               v-model="settings.mountBackgroundJob"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('mountBackgroundJob')"
        >
        <label :for="id + '-mount-background-job'">
          {{ t(appName, 'default to scheduling mount requests as background job') }}
        </label>
      </div>
      <div class="settings-option">
        <input :id="id + '-mount-by-left-click'"
               v-model="settings.mountByLeftClick"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('mountByLeftClick')"
        >
        <label :for="id + '-mount-by-left-click'">
          {{ t(appName, 'mount the archive as default action by a simple click') }}
        </label>
      </div>
    </NcSettingsSection>
    <NcSettingsSection :name="t(appName, 'Extraction Options')">
      <TextField :value.sync="settings.extractTargetTemplate"
                 :label="t(appName, 'Template for the default name of the extraction folder')"
                 :hint="t(appName, '{archiveFileName} will be replaced by the filename of the archive file without extensions.')"
                 placeholder="{archiveFileName}"
                 @submit="saveTextInput('extractTargetTemplate')"
      />
      <div class="settings-option">
        <input :id="id + '-extract-strip-common-prefix'"
               v-model="settings.extractStripCommonPathPrefixDefault"
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
               v-model="settings.extractTargetAutoRename"
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
               v-model="settings.extractBackgroundJob"
               type="checkbox"
               class="checkbox"
               @change="saveSetting('extractBackgroundJob')"
        >
        <label :for="id + '-extract-background-job'">
          {{ t(appName, 'default to scheduling extraction requests as background job') }}
        </label>
      </div>
    </NcSettingsSection>
  </div>
</template>
<script setup lang="ts">
import { appName } from './config.ts'
import {
  NcSettingsSection,
} from '@nextcloud/vue'
import {
  computed,
  reactive,
  ref,
} from 'vue'
import { translate as t } from '@nextcloud/l10n'
import TextField from '@rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue'
import cloudVersionClassesImport from './toolkit/util/cloud-version-classes.js'
import {
  fetchSettings,
  fetchSetting,
  saveConfirmedSetting,
  saveSimpleSetting,
} from './toolkit/util/settings-sync.ts'
import { v4 as uuidv4 } from 'uuid'
import logger from './console.ts'

const cloudVersionClasses = computed<string[]>(() => cloudVersionClassesImport)
const loading = ref(true)
const id = computed(() => uuidv4())

const settings = reactive({
  archiveSizeLimit: null as null|number,
  humanArchiveSizeLimit: '',
  archiveSizeLimitAdmin: null as null|number,
  humanArchiveSizeLimitAdmin: '',
  mountStripCommonPathPrefixDefault: false,
  mountPointTemplate: '{archiveFileName}',
  mountPointAutoRename: false,
  mountBackgroundJob: false,
  extractStripCommonPathPrefixDefault: false,
  extractTargetTemplate: '{archiveFileName}',
  extractTargetAutoRename: false,
  extractBackgroundJob: false,
})

const isLt = (a: null|undefined|number, b: null|undefined|number) => a! < b!

const getData = async () => {
  // slurp in all personal settings
  await fetchSettings({ section: 'personal', settings })
  loading.value = false
}
getData()

const saveTextInput = async (settingsKey: string, value?: string, force?: boolean) => {
  if (value === undefined) {
    value = settings[settingsKey] || ''
  }
  if (await saveConfirmedSetting({ value, section: 'admin', settingsKey, force, settings })) {
    if (settingsKey.endsWith('Converter')) {
      fetchSetting({ settingsKey: 'converters', section: 'admin', settings })
    }
  }
}

const saveSetting = async (settingsKey: string) => {
  if (loading.value) {
    // avoid ping-pong by reactivity
    logger.info('SKIPPING SETTINGS-SAVE DURING LOAD', settingsKey)
    return
  }
  return saveSimpleSetting({ settingsKey, section: 'personal', settings })
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
</style>
