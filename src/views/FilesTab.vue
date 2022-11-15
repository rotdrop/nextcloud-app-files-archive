<script>
/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2022 Claus-Justus Heine
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
  <div class="files-tab">
    <ul>
      <li class="files-tab-entry flex flex-center clickable"
          @click="showArchiveInfo = !showArchiveInfo"
      >
        <div class="files-tab-entry__avatar icon-info-white" />
        <div class="files-tab-entry__desc">
          <h5>{{ t(appName, 'Archive Information') }}</h5>
        </div>
        <Actions>
          <ActionButton v-model="showArchiveInfo"
                        :icon="'icon-triangle-' + (showArchiveInfo ? 's' : 'n')"
          />
        </Actions>
      </li>
      <li v-show="showArchiveInfo" class="files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <ul v-show="!loading" class="archive-info">
          <ListItem v-if="archiveStatus > 0"
                    :class="{ 'archive-error': archiveStatus > 0 }"
                    :title="t(appName, 'archive status')"
                    :bold="true"
                    :details="archiveStatusText"
          >
            <template #icon>
              <div class="icon-error" />
            </template>
          </ListItem>
          <ListItem :title="t(appName, 'archive format')"
                    :bold="true"
                    :details="archiveInfo.format || t(appName, 'unknown')"
          />
          <ListItem :title="t(appName, 'MIME type')"
                    :bold="true"
                    :details="archiveInfo.mimeType || t(appName, 'unknown')"
          />
          <ListItem :title="t(appName, 'backend driver')"
                    :bold="true"
                    :details="archiveInfo.backendDriver || t(appName, 'unknown')"
          />
          <ListItem :title="t(appName, 'uncompressed size')"
                    :bold="true"
                    :details="humanArchiveOriginalSize"
          />
          <ListItem :title="t(appName, 'compressed size')"
                    :bold="true"
                    :details="humanArchiveCompressedSize"
          />
          <ListItem v-if="humanArchiveCompressedSize !== humanArchiveFileSize"
                    :title="t(appName, 'archive file size')"
                    :bold="true"
                    :details="humanArchiveFileSize"
          />
          <ListItem :title="t(appName, '# archive members')"
                    :bold="true"
                    :details="numberOfArchiveMembers"
          />
          <ListItem :title="t(appName, 'common prefix')"
                    :bold="true"
                    :details="commonPathPrefix"
          />
          <ListItem v-if="archiveInfo.comment"
                    class="archive-comment"
                    :title="t(appName, 'creator\'s comment')"
                    :bold="true"
          >
            <template #subtitle>
              {{ archiveInfo.comment }}
            </template>
          </ListItem>
        </ul>
      </li>
      <li class="files-tab-entry flex flex-center">
        <div class="files-tab-entry__avatar icon-password-white" />
        <div class="files-tab-entry__desc">
          <h5>
            <span class="main-title">{{ t(appName, 'Passphrase') }}</span>
            <span v-if="!archivePassPhrase" class="title-annotation">({{ t(appName, 'unset') }})</span>
          </h5>
        </div>
        <Actions :force-menu="true">
          <ActionInput v-if="showArchivePassPhrase"
                       ref="archivePassPhrase"
                       :value="archivePassPhrase"
                       type="text"
                       icon="icon-password"
                       @submit="setPassPhrase"
          >
            {{ t(appName, 'archive passphrase') }}
          </ActionInput>
          <ActionInput v-else
                       ref="archivePassPhrase"
                       :value="archivePassPhrase"
                       type="password"
                       icon="icon-password"
                       @submit="setPassPhrase"
          >
            {{ t(appName, 'archive passphrase') }}
          </ActionInput>
          <ActionCheckBox @change="togglePassPhraseVisibility">
            {{ t(appName, 'Show Passphrase') }}
          </ActionCheckBox>
        </Actions>
      </li>
      <li class="files-tab-entry flex flex-center clickable"
          @click="showArchiveMounts = !showArchiveMounts"
      >
        <div class="files-tab-entry__avatar icon-external-white" />
        <div class="files-tab-entry__desc">
          <h5>
            <span class="main-title">{{ t(appName, 'Mount Points') }}</span>
            <span v-if="archiveMounted" class="title-annotation">({{ '' + archiveMounts.length }})</span>
            <span v-else class="title-annotation">({{ t(appName, 'not mounted') }})</span>
          </h5>
        </div>
        <Actions>
          <ActionButton v-model="showArchiveMounts"
                        :icon="'icon-triangle-' + (showArchiveMounts ? 's' : 'n')"
          />
        </Actions>
      </li>
      <li v-show="showArchiveMounts" class="directory-chooser files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <ul v-else-if="archiveMounted" class="archive-mounts">
          <ListItem v-for="mount in archiveMounts"
                    :key="mount.id"
                    :force-display-actions="true"
                    :bold="false"
          >
            <template #title>
              <a class="external icon-folder icon"
                 :target="openMountTarget"
                 :href="generateUrl('/apps/files') + '?dir=' + encodeURIComponent(mount.mountPointPath)"
              >
                {{ mount.mountPointPath }}
              </a>
            </template>
            <template #actions>
              <ActionButton icon="icon-delete" @click="unmount(mount, ...arguments)" />
            </template>
            <template v-if="mount.mountFlags & 1" #extra>
              <div>{{ t(appName, 'Common prefix {prefix} is stripped.', { prefix: commonPathPrefix }) }}</div>
            </template>
          </ListItem>
        </ul>
        <div v-else>
          <FilePrefixPicker v-model="archiveMountFileInfo"
                            :hint="t(appName, 'Not mounted, create a new mount-point:')"
                            :placeholder="t(appName, 'base-name')"
                            @update="mount"
          />
          <div class="flex flex-center">
            <div class="label"
                 @click="$refs.mountOptions.openMenu()"
            >
              {{ t(appName, 'Mount Options') }}
            </div>
            <Actions ref="mountOptions"
                     :force-menu="true"
            >
              <ActionCheckBox ref="mountStripCommonPathPrefix"
                              :checked="archiveMountStripCommonPathPrefix"
                              @change="archiveMountStripCommonPathPrefix = !archiveMountStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </ActionCheckBox>
            </Actions>
          </div>
        </div>
      </li>
      <li class="files-tab-entry flex flex-center clickable"
          @click="showArchiveExtraction = !showArchiveExtraction"
      >
        <div class="files-tab-entry__avatar icon-play-white" />
        <div class="files-tab-entry__desc">
          <h5>{{ t(appName, 'Extract Archive') }}</h5>
        </div>
        <Actions>
          <ActionButton v-model="showArchiveExtraction"
                        :icon="'icon-triangle-' + (showArchiveExtraction ? 's' : 'n')"
          />
        </Actions>
      </li>
      <li v-show="showArchiveExtraction" class="directory-chooser files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <div v-else>
          <FilePrefixPicker v-model="archiveExtractFileInfo"
                            :hint="t(appName, 'Choose a directory to extract the archive to:')"
                            :placeholder="t(appName, 'base-name')"
                            @update="extract"
          />
          <div class="flex flex-center">
            <div class="label"
                 @click="$refs.extractionOptions.openMenu()"
            >
              {{ t(appName, 'Extraction Options') }}
            </div>
            <Actions ref="extractionOptions"
                     :force-menu="true"
            >
              <ActionCheckBox :checked="archiveExtractStripCommonPathPrefix"
                              @change="archiveExtractStripCommonPathPrefix = !archiveExtractStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </ActionCheckBox>
            </Actions>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
<script>

import { appName } from '../config.js'
import Vue from 'vue'
import { getInitialState } from '../toolkit/services/InitialStateService.js'
import { generateUrl, generateRemoteUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import md5 from 'blueimp-md5'
import { showError, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import { formatFileSize } from '@nextcloud/files'
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import ActionCheckBox from '@nextcloud/vue/dist/Components/ActionCheckbox'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import ListItem from '@rotdrop/nextcloud-vue-components/lib/components/ListItem'
import SettingsInputText from '@rotdrop/nextcloud-vue-components/lib/components/SettingsInputText'
import FilePrefixPicker from '../components/FilePrefixPicker'
import axios from '@nextcloud/axios'
import { nextTick } from 'vue'

export default {
  name: 'FilesTab',
  components: {
    Actions,
    ActionButton,
    ActionInput,
    ActionCheckBox,
    ListItem,
    SettingsInputText,
    FilePrefixPicker,
  },
  mixins: [
  ],
  data() {
    return {
      fileList: undefined,
      fileInfo: {},
      fileName: undefined,
      showArchiveInfo: true,
      showArchivePassPhrase: false,
      showArchiveMounts: false,
      showArchiveExtraction: false,
      initialState: {},
      archiveInfo: {},
      archiveStatus: null,
      archiveMounts: [],
      archiveMounted: false,
      openMountTarget: md5(generateUrl('') + appName + '-open-archive-mount'),
      loading: 0,
      archiveMountFileInfo: {
        dirName: undefined,
        baseName: undefined,
      },
      archiveMountStripCommonPathPrefix: false,
      archiveExtractFileInfo: {
        dirName: undefined,
        baseName: undefined,
      },
      archiveExtractStripCommonPathPrefix: false,
      archivePassPhrase: undefined,
    };
  },
  created() {
    // this.getData()
  },
  mounted() {
    // this.getData()
  },
  computed: {
    archiveMountBaseName: {
      get() {
        return this.archiveMountFileInfo.baseName
      },
      set(value) {
        Vue.set(this.archiveMountFileInfo, 'baseName', value)
        return value
      }
    },
    archiveMountDirName: {
      get() {
        return this.archiveMountFileInfo.dirName
      },
      set(value) {
        Vue.set(this.archiveMountFileInfo, 'dirName', value)
        return value
      }
    },
    archiveMountPathName() {
      return this.archiveMountDirName + (this.archiveMountBaseName ? '/' + this.archiveMountBaseName : '')
    },
    archiveExtractBaseName: {
      get() {
        return this.archiveExtractFileInfo.baseName
      },
      set(value) {
        Vue.set(this.archiveExtractFileInfo, 'baseName', value)
        return value
      }
    },
    archiveExtractDirName: {
      get() {
        return this.archiveExtractFileInfo.dirName
      },
      set(value) {
        Vue.set(this.archiveExtractFileInfo, 'dirName', value)
        return value
      }
    },
    archiveExtractPathName() {
      return this.archiveExtractDirName + (this.archiveExtractBaseName ? '/' + this.archiveExtractBaseName : '')
    },
    archiveInfoText() {
      return JSON.stringify(this.archiveInfo, null, 2)
    },
    humanArchiveOriginalSize() {
      return !isNaN(parseInt(this.archiveInfo.originalSize))
        ? formatFileSize(this.archiveInfo.originalSize)
        : t(appName, 'unknown')
    },
    humanArchiveCompressedSize() {
      return !isNaN(parseInt(this.archiveInfo.compressedSize))
        ? formatFileSize(this.archiveInfo.compressedSize)
        : t(appName, 'unknown')
    },
    humanArchiveFileSize() {
      return !isNaN(parseInt(this.archiveInfo.size))
        ? formatFileSize(this.archiveInfo.size)
        : t(appName, 'unknown')
    },
    numberOfArchiveMembers() {
      if (!this.archiveInfo
          || this.archiveInfo.numberOfFiles === undefined
          || isNaN(parseInt('' + this.archiveInfo.numberOfFiles))) {
        return t(appName, 'unknown')
      }
      return '' + this.archiveInfo.numberOfFiles
    },
    commonPathPrefix() {
      return !this.archiveInfo
          || this.archiveInfo.commonPathPrefix === undefined
           ? t(appName, 'unknown')
           : '/' + this.archiveInfo.commonPathPrefix
    },
    archiveStatusText() {
      if (this.archiveStatus === 0) {
        return t(appName, 'ok')
      } else if (this.archiveStatus & 2) {
        return t(appName, 'zip bomb')
      } else if (this.archiveStatus & 1) {
        return t(appName, 'too large')
      }
      return t(appName, 'unknown')
    },
    mountPointTitle() {
      return t(appName, 'Mount Points')
           + ' ('
           + (this.archiveMounted
            ? this.archiveMounts.length
            : t(appName, 'not mounted')
           )
           + ')'
    },
  },
  methods: {
    info() {
      console.info.apply(null, arguments)
    },
     /**
     * Update current fileInfo and fetch new data
     * @param {Object} fileInfo the current file FileInfo
     */
    async update(fileInfo) {
      this.fileInfo = fileInfo
      this.fileName = fileInfo.path + '/' + fileInfo.name

      this.fileList = OCA.Files.App.currentFileList
      this.fileList.$el.off('updated').on('updated', function(event) {
        console.info('FILE LIST UPDATED, ARGS', arguments)
      })
      this.archiveMountBaseName = fileInfo.name.split('.')[0]
      this.archiveMountDirName = fileInfo.path

      this.archiveExtractBaseName = this.archiveMountBaseName
      this.archiveExtractDirName = this.archiveMountDirName

      this.getData()
    },
    /**
     * Fetch some needed data ...
     */
    async getData() {
      this.initialState = getInitialState()

      this.archiveMountStripCommonPathPrefix = !!this.initialState.mountStripCommonPathPrefixDefault
      this.archiveExtractStripCommonPathPrefix = !!this.initialState.extractStripCommonPathPrefixDefault

      this.getArchiveInfo(this.fileName)
      this.getArchiveMounts(this.fileName)
    },
    async getArchiveInfo(fileName) {
      ++this.loading
      fileName = encodeURIComponent(fileName)
      const url = generateUrl('/apps/' + appName + '/archive/info/{fileName}', { fileName })
      const requestData = {}
      if (this.archivePassPhrase) {
        requestData.passPhrase = this.archivePassPhrase
      }
      try {
        const response = await axios.post(url, requestData)
        const responseData = response.data
        this.archiveInfo = responseData.archiveInfo
        this.archiveStatus = responseData.archiveStatus
        for (const message of responseData.messages) {
          showInfo(message);
        }
        console.info('ARCHIVE INFO', this.archiveInfo)
      } catch (e) {
        console.error('ERROR', e)
        if (e.response && e.response.data) {
          const responseData = e.response.data
          this.archiveInfo = responseData.archiveInfo
          this.archiveStatus = responseData.archiveStatus
          if (responseData.messages) {
            for (const message of responseData.messages) {
              showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
            }
          }
        } else {
          this.archiveInfo = {}
        }
      }

      if (this.archiveInfo.defaultMountPoint) {
        this.archiveMountBaseName = this.archiveInfo.defaultMountPoint
      }
      if (this.archiveInfo.defaultTargetBaseName) {
        this.archiveExtractBaseName = this.archiveInfo.defaultTargetBaseName
      }

      --this.loading
    },
    async getArchiveMounts(fileName) {
      ++this.loading
      fileName = encodeURIComponent(fileName)
      const url = generateUrl('/apps/' + appName + '/archive/mount/{fileName}', { fileName })
      try {
        const response = await axios.get(url)
        const responseData = response.data
        this.archiveMounts = responseData.mounts
        this.archiveMounted = responseData.mounted
        for (const message of responseData.messages) {
          showInfo(message);
        }
        console.info('MOUNTS', this.archiveMounts)
      } catch (e) {
        console.error('ERROR', e)
        if (e.response && e.response.data) {
          const responseData = e.response.data
          this.archiveMounts = responseData.mounts
          this.archiveMounted = responseData.mounted
          if (responseData.messages) {
            for (const message of responseData.messages) {
              showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
            }
          }
        } else {
          this.archiveMounts = []
          this.archiveMounted = false
        }
      }
      // id="fileList"
      // data-path
      // data-file
      for (const mount of this.archiveMounts) {
        const pathComponents = mount.mountPointPath.split('/')
        const baseName = pathComponents.pop()
        const dirName = pathComponents.join('/')
        mount.baseName = baseName
        mount.dirName = dirName
        if (!this.archivePassPhrase && mount.archivePassPhrase) {
          this.archivePassPhrase = mount.archivePassPhrase
        }
        delete mount.archivePassPhrase
      }
      --this.loading
    },
    async mount() {
      const archivePath = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name)
      const mountPath = encodeURIComponent(this.archiveMountPathName)
      const url = generateUrl('/apps/' + appName + '/archive/mount/{archivePath}/{mountPath}', { archivePath, mountPath })
      this.fileList.showFileBusyState(this.fileInfo.name, true)
      const requestData = {}
      if (this.archivePassPhrase) {
        requestData.passPhrase = this.archivePassPhrase
      }
      requestData.stripCommonPathPrefix = !!this.archiveMountStripCommonPathPrefix;
      try {
        const response = await axios.post(url, requestData)
        this.getArchiveMounts(this.fileName)
        if (this.archiveMountDirName === this.fileInfo.path) {
          this.fileList.reload();
        }
      } catch (e) {
        console.error('ERROR', e)
        if (e.response) {
          const messages = []
          if (e.response.data) {
            const responseData = e.response.data
            if (responseData.messages) {
              messages.splice(messages.length, 0, responseData.messages)
            }
          }
          if (!messages.length) {
            messages.push(t(appName, 'Mount request failed with error {status}, "{statusText}".', e.response))
          }
          for (const message of messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
          }
        }
      }
      this.fileList.showFileBusyState(this.fileInfo.name, false)
    },
    async unmount(mount) {
      if (mount.dirName === this.fileInfo.dir && !this.fileList.inList(mount.baseName)) {
        this.getArchiveMounts()
        return
      }
      const cloudUser = getCurrentUser()
      const url = generateRemoteUrl('dav/files/' + cloudUser.uid + mount.mountPointPath)
      this.fileList.showFileBusyState(this.fileInfo.name, true)
      try {
        const response = await axios.delete(url)
        if (mount.dirName === this.fileInfo.dir) {
          this.fileList.remove(mount.baseName)
        }
        this.archiveMounted = false
      } catch (e) {
        console.error('ERROR', e)
        const messages = []
        if (e.response) {
          // attempt parsing Sabre exception is available
          const xml = e.response.request.responseXML
          if (xml && xml.documentElement.localName === 'error' && xml.documentElement.namespaceURI === 'DAV:') {
            const xmlMessages = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'message');
            // const exceptions = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'exception');
            for (const message of xmlMessages) {
              messages.push(message.textContent)
            }
          }
          if (e.response.data) {
            const responseData = e.response.data
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages)
            }
          }
          if (!messages.length) {
            messages.push(t(appName, 'Unmount request failed with error {status}, "{statusText}".', e.response))
          }
          for (const message of messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
          }
          if (e.response.status === 404) {
            this.getArchiveMounts()
          }
        }
      }
      this.fileList.showFileBusyState(this.fileInfo.name, false)
    },
    async extract() {
      const archivePath = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name)
      const targetPath = encodeURIComponent(this.archiveExtractPathName)
      const url = generateUrl('/apps/' + appName + '/archive/extract/{archivePath}/{targetPath}', { archivePath, targetPath })
      this.fileList.showFileBusyState(this.fileInfo.name, true)
      const requestData = {}
      if (this.archivePassPhrase) {
        requestData.passPhrase = this.archivePassPhrase
      }
      requestData.stripCommonPathPrefix = !!this.archiveExtractStripCommonPathPrefix
      try {
        const response = await axios.post(url, requestData)
        if (this.archiveExtractDirName === this.fileInfo.path) {
          this.fileList.reload();
        }
      } catch (e) {
        console.error('ERROR', e)
        if (e.response) {
          const messages = []
          if (e.response.data) {
            const responseData = e.response.data
            if (responseData.messages) {
              messages.splice(messages.length, 0, responseData.messages)
            }
          }
          if (!messages.length) {
            messages.push(t(appName, 'Archive-extraction failed with error {status}, "{statusText}".', e.response))
          }
          for (const message of messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
          }
        }
      }
      this.fileList.showFileBusyState(this.fileInfo.name, false)
    },
    async setPassPhrase() {
      const newPassPhrase = this.showArchivePassPhrase
        ? this.$refs.archivePassPhrase.$el.querySelector('input[type="text"]').value
        : this.$refs.archivePassPhrase.$el.querySelector('input[type="password"]').value
      this.archivePassPhrase = newPassPhrase

      // patch it into existing mounts if any
      const archivePath = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name)
      const url = generateUrl('/apps/' + appName + '/archive/mount/{archivePath}', { archivePath })
      this.fileList.showFileBusyState(this.fileInfo.name, true)
      const requestData = {
        changeSet: {
          archivePassPhrase: this.archivePassPhrase,
        },
      }
      try {
        const response = await axios.patch(url, requestData)
      } catch (e) {
        console.error('ERROR', e)
        if (e.response) {
          const messages = []
          if (e.response.data) {
            const responseData = e.response.data
            if (responseData.messages) {
              messages.splice(messages.length, 0, responseData.messages)
            }
          }
          if (!messages.length) {
            messages.push(t(appName, 'Patching the pass-phrase failed with error {status}, "{statusText}".', e.response))
          }
          for (const message of messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
          }
        }
      }
      this.fileList.showFileBusyState(this.fileInfo.name, false)
    },
    async togglePassPhraseVisibility() {
      // this is sooo complicated because the NC Action controls are
      // seemingly only pro-forma vue-controls. There is no working
      // v-model support, e.g.
      let visibleElement = this.showArchivePassPhrase
        ? this.$refs.archivePassPhrase.$el.querySelector('input[type="text"]')
        : this.$refs.archivePassPhrase.$el.querySelector('input[type="password"]')
      const currentValue = visibleElement.value

      this.showArchivePassPhrase = !this.showArchivePassPhrase
      await nextTick()

      visibleElement = this.showArchivePassPhrase
        ? this.$refs.archivePassPhrase.$el.querySelector('input[type="text"]')
        : this.$refs.archivePassPhrase.$el.querySelector('input[type="password"]')
      visibleElement.value = currentValue
    }
  },
}
</script>
<style lang="scss" scoped>
.files-tab {
  .flex {
    display:flex;
    &.flex-center {
      align-items:center;
    }
    &.flex-wrap {
      flex-wrap:wrap;
    }
    .flex-grow {
      flex-grow:1;
    }
  }
  a.icon {
    background-position: left;
    padding-left:20px;
  }
  ::v-deep .archive-info {
    .list-item__wrapper{
      &:not(.archive-comment) {
        .list-item-content__wrapper {
          height:24px;
        }
        a.list-item {
          padding:0;
        }
      }
      &.archive-comment {
        a.list-item {
          padding:8px 0;
          .line-two__subtitle {
            max-width:100%;
            white-space:normal;
          }
        }
      }
      &.archive-error {
        .line-one__details {
          font-weight:bold;
          font-style:italic;
          color:red;
        }
      }
    }
  }
  .files-tab-entry {
    min-height:44px;
    &.clickable {
      &, & * {
        cursor:pointer;
      }
    }
    .files-tab-entry__avatar {
      width: 32px;
      height: 32px;
      line-height: 32px;
      font-size: 18px;
      background-color: var(--color-text-maxcontrast);
      border-radius: 50%;
      flex-shrink: 0;
    }
    .files-tab-entry__desc {
      flex: 1 1;
      padding: 8px;
      line-height: 1.2em;
      min-width:0;
      h5 {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: inherit;
      }
    }
    &.directory-chooser {
      .dirname {
        font-weight:bold;
        font-family:monospace;
        .button {
          display:block;
        }
      }
      .label {
        padding-right:0.5ex;
      }
    }
    /* ::v-deep .archive-mounts {
       .list-item-content > .list-item-content__actions {
       display: block !important;
       }
       } */
  }
}
</style>
