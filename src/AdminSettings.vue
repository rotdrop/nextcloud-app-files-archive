<!--
 - @copyright Copyright (c) 2022-2025 Claus-Justus Heine <himself@claus-justus-heine.de>
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
      <TextField :value.sync="settings.humanArchiveSizeLimit"
                 :label="t(appName, 'Archive Size Limit')"
                 :hint="t(appName, 'Disallow archive extraction for archives with decompressed size larger than this limit.')"
                 :disabled="loading"
                 @submit="saveTextInput('archiveSizeLimit', settings.humanArchiveSizeLimit)"
      />
    </NcSettingsSection>
    <NcSettingsSection :name="t(appName, 'Diagnostics')" class="diagnostics">
      <h3>{{ t(appName, "Archive Formats") }}</h3>
      <!-- eslint-disable-next-line vue/no-v-html -->
      <pre :class="{ loading: diagnostics.formats === null, diagnostics_output: true, ansi_color_bg_black: true }" v-html="diagnostics.formats" />
      <h3>{{ t(appName, "Supported Drivers") }}</h3>
      <!-- eslint-disable-next-line vue/no-v-html -->
      <pre :class="{ loading: diagnostics.drivers === null, diagnostics_output: true, ansi_color_bg_black: true }" v-html="diagnostics.drivers" />
    </NcSettingsSection>
  </div>
</template>
<script setup lang="ts">
import { appName } from './config.ts'
import {
  NcSettingsSection,
} from '@nextcloud/vue'
import axios from '@nextcloud/axios'
import { translate as t } from '@nextcloud/l10n'
import { showError /* , showSuccess, showInfo, TOAST_PERMANENT_TIMEOUT */ } from '@nextcloud/dialogs'
import {
  computed,
  reactive,
  ref,
} from 'vue'
import {
  fetchSettings,
  saveConfirmedSetting,
} from './toolkit/util/settings-sync.ts'
import { generateUrl as generateAppUrl } from './toolkit/util/generate-url.ts'
import TextField from '@rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue'
import cloudVersionClassesImport from './toolkit/util/cloud-version-classes.ts'
import logger from './console.ts'

const cloudVersionClasses = computed<string[]>(() => cloudVersionClassesImport)
const loading = ref(true)

const settings = reactive({
  archiveSizeLimit: 1 << 32,
  humanArchiveSizeLimit: '',
})

const diagnostics = reactive({
  formats: null as string|null,
  drivers: null as string|null,
})

const getData = async () => {
  // slurp in all settings
  await fetchSettings({ section: 'admin', settings })
  loading.value = false
  getFormatsMatrix()
  getDriversStatus()
}
getData()

const saveTextInput = async (settingsKey: string, value?: string, force?: boolean) => {
  if (value === undefined) {
    value = settings[settingsKey] || ''
  }
  return saveConfirmedSetting({ value, section: 'admin', settingsKey, force, settings })
}

const getFormatsMatrix = async () => {
  try {
    const response = await axios.get(generateAppUrl('diagnostics/archive/formats'))
    diagnostics.formats = response?.data?.html || null
    if (!diagnostics.formats) {
      logger.error('UNEXPECTED RESPONSE', response)
    }
    return
  } catch (e) {
    logger.error('ERROR', e)
  }
  showError(t(appName, 'Unable to query the archive-format support matrix.'))
}

const getDriversStatus = async () => {
  try {
    const response = await axios.get(generateAppUrl('diagnostics/archive/drivers'))
    diagnostics.drivers = response?.data?.html || null
    if (!diagnostics.formats) {
      logger.error('UNEXPECTED RESPONSE', response)
    }
    return
  } catch (e) {
    logger.error('ERROR', e)
  }
  showError(t(appName, 'Unable to query the information about the available archive backend drivers.'))
}
</script>
<style lang="scss" scoped>
.cloud-version {
  --cloud-theme-filter: var(--background-invert-if-dark);
  &.cloud-version-major-24 {
    --cloud-theme-filter: none;
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
  .diagnostics_output {
    min-height: 2ex;
    max-width: 100%;
    overflow-x: auto;
    &, * {
      font-family: monospace;
      font-size: 80%;
      line-height: normal;
    }
  }
}
// solarized colors for SensioLabs\AnsiConverter
[data-themes*='dark'] {
  .templateroot::v-deep .diagnostics *:not(.loading) {
    &.ansi_color_fg_black { color: #073642 }
    &.ansi_color_bg_black { background-color: #073642 }
    &.ansi_color_fg_red { color: #dc322f }
    &.ansi_color_bg_red { background-color: #dc322f }
    &.ansi_color_fg_green { color: #859900 }
    &.ansi_color_bg_green { background-color: #859900 }
    &.ansi_color_fg_yellow { color: #b58900 }
    &.ansi_color_bg_yellow { background-color: #b58900 }
    &.ansi_color_fg_blue { color: #268bd2 }
    &.ansi_color_bg_blue { background-color: #268bd2 }
    &.ansi_color_fg_magenta { color: #d33682 }
    &.ansi_color_bg_magenta { background-color: #d33682 }
    &.ansi_color_fg_cyan { color: #2aa198 }
    &.ansi_color_bg_cyan { background-color: #2aa198 }
    &.ansi_color_fg_white { color: #eee8d5 }
    &.ansi_color_bg_white { background-color: #eee8d5 }
    &.ansi_color_fg_brblack { color: #002b36 }
    &.ansi_color_bg_brblack { background-color: #002b36 }
    &.ansi_color_fg_brred { color: #cb4b16 }
    &.ansi_color_bg_brred { background-color: #cb4b16 }
    &.ansi_color_fg_brgreen { color: #586e75 }
    &.ansi_color_bg_brgreen { background-color: #586e75 }
    &.ansi_color_fg_bryellow { color: #657b83 }
    &.ansi_color_bg_bryellow { background-color: #657b83 }
    &.ansi_color_fg_brblue { color: #839496 }
    &.ansi_color_bg_brblue { background-color: #839496 }
    &.ansi_color_fg_brmagenta { color: #6c71c4 }
    &.ansi_color_bg_brmagenta { background-color: #6c71c4 }
    &.ansi_color_fg_brcyan { color: #93a1a1 }
    &.ansi_color_bg_brcyan { background-color: #93a1a1 }
    &.ansi_color_fg_brwhite { color: #fdf6e3 }
    &.ansi_color_bg_brwhite { background-color: #fdf6e3 }
  }
}
[data-themes*='light'], [data-themes*='default'] {
  .templateroot::v-deep .diagnostics *:not(.loading) {
    &.ansi_color_fg_black { color: #eee8d5 }
    &.ansi_color_bg_black { background-color: #eee8d5 }
    &.ansi_color_fg_red { color: #dc322f }
    &.ansi_color_bg_red { background-color: #dc322f }
    &.ansi_color_fg_green { color: #859900 }
    &.ansi_color_bg_green { background-color: #859900 }
    &.ansi_color_fg_yellow { color: #b58900 }
    &.ansi_color_bg_yellow { background-color: #b58900 }
    &.ansi_color_fg_blue { color: #268bd2 }
    &.ansi_color_bg_blue { background-color: #268bd2 }
    &.ansi_color_fg_magenta { color: #d33682 }
    &.ansi_color_bg_magenta { background-color: #d33682 }
    &.ansi_color_fg_cyan { color: #2aa198 }
    &.ansi_color_bg_cyan { background-color: #2aa198 }
    &.ansi_color_fg_white { color: #073642 }
    &.ansi_color_bg_white { background-color: #073642 }
    &.ansi_color_fg_brblack { color: #fdf6e3 }
    &.ansi_color_bg_brblack { background-color: #fdf6e3 }
    &.ansi_color_fg_brred { color: #cb4b16 }
    &.ansi_color_bg_brred { background-color: #cb4b16 }
    &.ansi_color_fg_brgreen { color: #93a1a1 }
    &.ansi_color_bg_brgreen { background-color: #93a1a1 }
    &.ansi_color_fg_bryellow { color: #839496 }
    &.ansi_color_bg_bryellow { background-color: #839496 }
    &.ansi_color_fg_brblue { color: #657b83 }
    &.ansi_color_bg_brblue { background-color: #657b83 }
    &.ansi_color_fg_brmagenta { color: #6c71c4 }
    &.ansi_color_bg_brmagenta { background-color: #6c71c4 }
    &.ansi_color_fg_brcyan { color: #586e75 }
    &.ansi_color_bg_brcyan { background-color: #586e75 }
    &.ansi_color_fg_brwhite { color: #002b36 }
    &.ansi_color_bg_brwhite { background-color: #002b36 }
  }
}
</style>
