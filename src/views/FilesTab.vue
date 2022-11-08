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
      <li class="files-tab-entry flex flex-center clickable"
          @click="showArchiveMounts = !showArchiveMounts"
      >
        <div class="files-tab-entry__avatar icon-external-white" />
        <div class="files-tab-entry__desc">
          <h5>{{ t(appName, 'Mount Points') }}</h5>
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
                    title=""
                    :bold="false"
          >
            <template #icon>
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
          </ListItem>
        </ul>
        <div v-else>
          <div class="hint">
            {{ t(appName, 'Not mounted, create a new mount-point:') }}
          </div>
          <div class="flex flex-center">
            <div class="dirname">
              <a href="#"
                 class="file-picker button"
                 @click="openFilePicker('archiveMount', ...arguments)"
              >
                {{ archiveMountDirName + (archiveMountDirName !== '/' ? '/' : '') }}
              </a>
            </div>
            <SettingsInputText v-model="archiveMountBaseName"
                               label=""
                               class="flex-grow"
                               @update="mount"
            />
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
          <div class="hint">
            {{ t(appName, 'Choose a directory to extract the archive to:') }}
          </div>
          <div class="flex flex-center">
            <div class="dirname">
              <a href="#"
                 class="file-picker button"
                 @click="openFilePicker('archiveExtract', ...arguments)"
              >
                {{ archiveExtractDirName + (archiveExtractDirName !== '/' ? '/' : '') }}
              </a>
            </div>
            <SettingsInputText v-model="archiveExtractBaseName"
                               label=""
                               class="flex-grow"
                               @update="extract"
            />
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
<script>

import { appName } from '../config.js'
import { getInitialState } from '../services/InitialStateService.js'
import { generateUrl, generateRemoteUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import md5 from 'blueimp-md5'
import { getFilePickerBuilder, showError, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import { formatFileSize } from '@nextcloud/files'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import SettingsInputText from '../components/SettingsInputText'
import axios from '@nextcloud/axios'

export default {
  name: 'FilesTab',
  components: {
    Actions,
    ActionButton,
    ListItem,
    SettingsInputText,
  },
  mixins: [
  ],
  data() {
    return {
      fileList: undefined,
      fileInfo: {},
      fileName: undefined,
      showArchiveInfo: true,
      showArchiveMounts: false,
      showArchiveExtraction: false,
      initialState: {},
      archiveInfo: {},
      archiveStatus: null,
      archiveMounts: [],
      archiveMounted: false,
      openMountTarget: md5(generateUrl('') + appName + '-open-archive-mount'),
      loading: 0,
      archiveMountDirName: undefined,
      archiveMountBaseName: undefined,
      archiveExtractDirName: undefined,
      archiveExtractBaseName: undefined,
    };
  },
  created() {
    // this.getData()
  },
  mounted() {
    // this.getData()
  },
  computed: {
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
          || this.archiveInfo.numberOfFiles == undefined
          || isNaN(parseInt('' + this.archiveInfo.numberOfFiles))) {
        t(appName, 'unknown')
      }
      return '' + this.archiveInfo.numberOfFiles
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
        console.info('ARGS', arguments)
      })
      const components = fileInfo.name.split('.')
      if (components.length > 1) {
        components.pop()
      }
      this.archiveMountBaseName = components.join('.')
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

      this.getArchiveInfo(this.fileName)
      this.getArchiveMounts(this.fileName)
    },
    async getArchiveInfo(fileName) {
      ++this.loading
      fileName = encodeURIComponent(fileName)
      const url = generateUrl('/apps/' + appName + '/archive/info/{fileName}', { fileName })
      try {
        const response = await axios.get(url)
        const responseData = response.data
        this.archiveInfo = responseData.archiveInfo
        this.archiveStatus = responseData.archiveStatus
        for (const message of responseData.messages) {
          showInfo(message);
        }
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
      }
      --this.loading
    },
    async mount() {
      const archivePath = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name)
      const mountPath = encodeURIComponent(this.archiveMountDirName + (this.archiveMountBaseName ? '/' + this.archiveMountBaseName : ''))
      const url = generateUrl('/apps/' + appName + '/archive/mount/{archivePath}/{mountPath}', { archivePath, mountPath })
      this.fileList.showFileBusyState(this.fileInfo.name, true)
      try {
        const response = await axios.post(url)
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
      const targetPath = encodeURIComponent(this.archiveExtractDirName + (this.archiveExtractBaseName ? '/' + this.archiveExtractBaseName : ''))
      const url = generateUrl('/apps/' + appName + '/archive/extract/{archivePath}/{targetPath}', { archivePath, targetPath })
      this.fileList.showFileBusyState(this.fileInfo.name, true)
      try {
        const response = await axios.post(url)
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
    async openFilePicker(dataPrefix) {
      console.info('ARGUMENTS', arguments)

      const picker = getFilePickerBuilder(t(appName, 'Choose a prefix-folder'))
        .startAt(this[dataPrefix + 'DirName'])
        .setMultiSelect(false)
        .setModal(true)
        .setType(1)
        .setMimeTypeFilter(['httpd/unix-directory'])
        .allowDirectories()
        .build()

      const dir = await picker.pick() || '/'
      if (!dir.startsWith('/')) {
        showError(t(appName, 'Invalid path selected: "{dir}".', { dir }), { timeout: TOAST_PERMANENT_TIMEOUT })
      } else  {
        showInfo(t(appName, 'Selected path: "{dir}/{base}/".', { dir, base: this[dataPrefix + 'BaseName'] }))
        this[dataPrefix + 'DirName'] = dir
      }
    },
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
      }
    }
  }
}
</style>
