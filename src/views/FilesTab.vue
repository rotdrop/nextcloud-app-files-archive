<!--
  - @author Claus-Justus Heine <himself@claus-justus-heine.de>
  - @copyright 2022, 2023, 2024 Claus-Justus Heine
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
  -
  -->
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
        <NcActions>
          <NcActionButton v-model="showArchiveInfo"
                          :icon="'icon-triangle-' + (showArchiveInfo ? 'n' : 's')"
          />
        </NcActions>
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
        <NcActions :force-menu="true">
          <NcActionInput v-if="showArchivePassPhrase"
                         ref="archivePassPhrase"
                         :value="archivePassPhrase"
                         type="text"
                         icon="icon-password"
                         @submit="setPassPhrase"
          >
            {{ t(appName, 'archive passphrase') }}
          </NcActionInput>
          <NcActionInput v-else
                         ref="archivePassPhrase"
                         :value="archivePassPhrase"
                         type="password"
                         icon="icon-password"
                         @submit="setPassPhrase"
          >
            {{ t(appName, 'archive passphrase') }}
          </NcActionInput>
          <NcActionCheckbox @change="togglePassPhraseVisibility">
            {{ t(appName, 'Show Passphrase') }}
          </NcActionCheckbox>
        </NcActions>
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
        <NcActions>
          <NcActionButton v-model="showArchiveMounts"
                          :icon="'icon-triangle-' + (showArchiveMounts ? 'n' : 's')"
          />
        </NcActions>
      </li>
      <li v-show="showArchiveMounts" class="directory-chooser files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <ul v-else-if="archiveMounted" class="archive-mounts">
          <ListItem v-for="mountPoint in archiveMounts"
                    :key="mountPoint.id"
                    :force-display-actions="true"
                    :bold="false"
          >
            <template #title>
              <a class="external icon-folder icon"
                 :target="openMountTarget"
                 :href="generateUrl('/apps/files') + '?dir=' + encodeURIComponent(mountPoint.mountPointPath)"
              >
                {{ mountPoint.mountPointPath }}
              </a>
            </template>
            <template #actions>
              <NcActionButton icon="icon-delete" @click="unmount(mountPoint, ...arguments)" />
            </template>
            <template v-if="mountPoint.mountFlags & 1" #extra>
              <div>{{ t(appName, 'Common prefix {prefix} is stripped.', { prefix: commonPathPrefix }) }}</div>
            </template>
          </ListItem>
        </ul>
        <div v-else>
          <FilePrefixPicker v-model="archiveMountFileInfo"
                            :hint="t(appName, 'Not mounted, create a new mount point:')"
                            :placeholder="t(appName, 'base name')"
                            @update="mountArchive"
          />
          <div class="flex flex-center">
            <div class="label"
                 @click="$refs.mountOptions.openMenu()"
            >
              {{ t(appName, 'Mount Options') }}
            </div>
            <NcActions ref="mountOptions"
                       :force-menu="true"
            >
              <NcActionCheckbox ref="mountStripCommonPathPrefix"
                                :checked="archiveMountStripCommonPathPrefix"
                                @change="archiveMountStripCommonPathPrefix = !archiveMountStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </NcActionCheckbox>
              <NcActionCheckbox ref="mountBackgroundJob"
                                :checked="archiveMountBackgroundJob"
                                @change="archiveMountBackgroundJob = !archiveMountBackgroundJob"
              >
                {{ t(appName, 'schedule as background job') }}
              </NcActionCheckbox>
            </NcActions>
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
        <NcActions>
          <NcActionButton v-model="showArchiveExtraction"
                          :icon="'icon-triangle-' + (showArchiveExtraction ? 'n' : 's')"
          />
        </NcActions>
      </li>
      <li v-show="showArchiveExtraction" class="directory-chooser files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <div v-else>
          <FilePrefixPicker v-model="archiveExtractFileInfo"
                            :hint="t(appName, 'Choose a directory to extract the archive to:')"
                            :placeholder="t(appName, 'basename')"
                            @update="extractArchive"
          />
          <div class="flex flex-center">
            <div class="label"
                 @click="$refs.extractionOptions.openMenu()"
            >
              {{ t(appName, 'Extraction Options') }}
            </div>
            <NcActions ref="extractionOptions"
                       :force-menu="true"
            >
              <NcActionCheckbox :checked="archiveExtractStripCommonPathPrefix"
                                @change="archiveExtractStripCommonPathPrefix = !archiveExtractStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </NcActionCheckbox>
              <NcActionCheckbox ref="extractBackgroundJob"
                                :checked="archiveExtractBackgroundJob"
                                @change="archiveExtractBackgroundJob = !archiveExtractBackgroundJob"
              >
                {{ t(appName, 'schedule as background job') }}
              </NcActionCheckbox>
            </NcActions>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>
<script>

import { appName } from '../config.js'
import { set as vueSet, nextTick } from 'vue'
import { getInitialState } from '../toolkit/services/InitialStateService.js'
import { generateUrl, generateRemoteUrl } from '@nextcloud/router'
import { emit, subscribe } from '@nextcloud/event-bus'
import { getCurrentUser } from '@nextcloud/auth'
import generateAppUrl from '../toolkit/util/generate-url.js'
import { fileInfoToNode } from '../toolkit/util/file-node-helper.js'
import md5 from 'blueimp-md5'
import { showError, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'

import { formatFileSize } from '@nextcloud/files'
import { NcActionInput, NcActionCheckbox, NcActions, NcActionButton } from '@nextcloud/vue'
import ListItem from '@rotdrop/nextcloud-vue-components/lib/components/ListItem.vue'
import FilePrefixPicker from '../components/FilePrefixPicker.vue'
import axios from '@nextcloud/axios'

const mountsPollingInterval = 30 * 1000

export default {
  name: 'FilesTab',
  components: {
    NcActions,
    NcActionButton,
    NcActionInput,
    NcActionCheckbox,
    ListItem,
    FilePrefixPicker,
  },
  mixins: [
  ],
  data() {
    return {
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
      openMountTarget: md5(generateUrl('') + appName + '-open-archive-mount'),
      loading: 0,
      archiveMountFileInfo: {
        dirName: undefined,
        baseName: undefined,
      },
      archiveMountStripCommonPathPrefix: false,
      archiveMountBackgroundJob: false,
      archiveExtractFileInfo: {
        dirName: undefined,
        baseName: undefined,
      },
      archiveExtractStripCommonPathPrefix: false,
      archiveExtractBackgroundJob: false,
      archivePassPhrase: undefined,
      //
      backgroundMountsTimer: undefined,
    }
  },
  computed: {
    archiveFileId() {
      return this.fileInfo?.id
    },
    archiveFileDirName() {
      return this.fileInfo.path
    },
    archiveFileBaseName() {
      return this.fileInfo.name
    },
    archiveFilePathName() {
      return this.fileInfo.path + '/' + this.fileInfo.name
    },
    archiveMounted() {
      return this.archiveMounts.length > 0
    },
    archiveMountBaseName: {
      get() {
        return this.archiveMountFileInfo.baseName
      },
      set(value) {
        vueSet(this.archiveMountFileInfo, 'baseName', value)
        return value
      },
    },
    archiveMountDirName: {
      get() {
        return this.archiveMountFileInfo.dirName
      },
      set(value) {
        vueSet(this.archiveMountFileInfo, 'dirName', value)
        return value
      },
    },
    archiveMountPathName() {
      return this.archiveMountDirName + (this.archiveMountBaseName ? '/' + this.archiveMountBaseName : '')
    },
    archiveExtractBaseName: {
      get() {
        return this.archiveExtractFileInfo.baseName
      },
      set(value) {
        vueSet(this.archiveExtractFileInfo, 'baseName', value)
        return value
      },
    },
    archiveExtractDirName: {
      get() {
        return this.archiveExtractFileInfo.dirName
      },
      set(value) {
        vueSet(this.archiveExtractFileInfo, 'dirName', value)
        return value
      },
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
        + (this.archiveMounted ? this.archiveMounts.length : t(appName, 'not mounted'))
        + ')'
    },
  },
  created() {
    // this.getData()
    subscribe('files:node:deleted', this.onMountPointDeleted)
    subscribe('files:node:renamed', this.onMountPointRenamed)

    subscribe('notifications:notification:received', (event) => this.onNotification(event))
  },
  mounted() {
    // this.getData()
  },
  beforeDestroy() {
    if (this.backgroundMountsTimer) {
      clearInterval(this.backgroundMountsTimer)
      this.backgroundMountsTimer = undefined
    }
  },
  methods: {
    info() {
      console.info.apply(null, arguments)
    },
    setBusyState(/* state */) {
      // This cannot be used any longer. How to?
      // this.fileList.showFileBusyState(this.fileInfo.name, state)
    },
    /**
     * Update current fileInfo and fetch new data
     * @param {object} fileInfo the current file FileInfo
     */
    async update(fileInfo) {
      console.info('FILE INFO', fileInfo)

      this.fileInfo = fileInfo
      this.fileName = fileInfo.path + '/' + fileInfo.name

      /* this.fileList = OCA.Files.App.currentFileList
       * this.fileList.$el.off('updated').on('updated', function(event) {
       *   console.info('FILE LIST UPDATED, ARGS', arguments)
       * }) */
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
      this.archiveMountBackgroundJob = !!this.initialState.mountBackgroundJob
      this.archiveExtractBackgroundJob = !!this.initialState.extractBackgroundJob

      this.getArchiveInfo(this.fileName)
      this.refreshArchiveMounts(this.fileName, true)
    },
    async getArchiveInfo(fileName) {
      ++this.loading
      fileName = encodeURIComponent(fileName)
      const url = generateAppUrl('archive/info/{fileName}', { fileName })
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
          showInfo(message)
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
    async refreshArchiveMounts(filename, noEmit) {
      const oldMounts = [...this.archiveMounts]
      const mounts = await this.getArchiveMounts(filename, false)
      this.archiveMounts = mounts.mounts
      if (noEmit) {
        // do no emit birth during initialization
        return
      }
      // emit birth and death signals as needed, in order to update
      // the frontend file-listing. The computational effort is
      // quadratic, but we are talking here about the common case that
      // there is only a single mount -- or by accident another
      // one. So what.
      const newMounts = this.archiveMounts.filter((mount) => oldMounts.findIndex((oldMount) => mount.mountPoint.fileid === oldMount.mountPoint.fileid) === -1)
      const deletedMounts = oldMounts.filter((oldMount) => this.archiveMounts.findIndex((mount) => mount.mountPoint.fileid === oldMount.mountPoint.fileid) === -1)
      for (const mount of deletedMounts) {
        const node = fileInfoToNode(mount.mountPoint)

        console.info('EMIT DELETED', node)
        emit('files:node:deleted', node)
      }
      for (const mount of newMounts) {
        const node = fileInfoToNode(mount.mountPoint)

        console.info('EMIT CREATED', node)
        emit('files:node:created', node)
      }
    },
    async getArchiveMounts(fileName, silent) {
      const result = {
        mounts: [],
        mounted: false,
      }
      if (silent !== true) {
        ++this.loading
      }
      fileName = encodeURIComponent(fileName)
      const url = generateAppUrl('archive/mount/{fileName}', { fileName })
      try {
        const response = await axios.get(url)
        const responseData = response.data
        result.mounts = responseData.mounts
        result.mounted = responseData.mounted
        for (const message of responseData.messages) {
          showInfo(message)
        }
        console.info('MOUNTS', this.archiveMounts)
      } catch (e) {
        console.error('ERROR', e)
        if (e.response && e.response.data) {
          const responseData = e.response.data
          result.mounts = responseData.mounts
          result.mounted = responseData.mounted
          if (responseData.messages) {
            for (const message of responseData.messages) {
              showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
            }
          }
        }
      }
      // id="fileList"
      // data-path
      // data-file
      for (const mount of result.mounts) {
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
      if (silent !== true) {
        --this.loading
      }
      return result
    },
    async backgroundMountsPoller(archiveMountIds) {
      const { mounts } = await this.getArchiveMounts(this.fileName, true)
      let mountingFinished = mounts.length !== archiveMountIds.length
      if (!mountingFinished) {
        const mountIds = mounts.map(mount => mount.id).sort()
        for (let i = 0; i < mountIds.length; ++i) {
          if (mountIds[i] !== archiveMountIds[i]) {
            mountingFinished = true
            break
          }
        }
      }
      if (!mountingFinished) {
        this.backgroundMountsTimer = setTimeout(() => this.backgroundMountsPoller(archiveMountIds), mountsPollingInterval)
      } else {
        this.backgroundMountsTimer = undefined
        this.archiveMounts = mounts
        this.refreshArchiveMounts(this.fileName)
        if (this.archiveMountDirName === this.fileInfo.path) {
          // @todo emit signal
          // this.fileList.reload()
        }
      }
    },
    async mountArchive() {
      const archivePath = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name)
      const mountPath = encodeURIComponent(this.archiveMountPathName)
      const urlTemplate = this.archiveMountBackgroundJob
        ? 'archive/schedule/mount/{archivePath}/{mountPath}'
        : 'archive/mount/{archivePath}/{mountPath}'
      const url = generateAppUrl(urlTemplate, { archivePath, mountPath })
      this.setBusyState(true)
      const requestData = {}
      if (this.archivePassPhrase) {
        requestData.passPhrase = this.archivePassPhrase
      }
      requestData.stripCommonPathPrefix = !!this.archiveMountStripCommonPathPrefix
      try {
        const response = await axios.post(url, requestData)
        if (this.archiveMountBackgroundJob) {
          const archiveMountIds = this.archiveMounts.map(mount => mount.id).sort()
          this.backgroundMountsTimer = setTimeout(() => this.backgroundMountsPoller(archiveMountIds), mountsPollingInterval)
        } else {
          const newMount = response.data
          const newFileId = newMount.mountPoint.fileid
          if (this.archiveMounts.findIndex((mount) => mount.mountPoint.fileid === newFileId) === -1) {
            this.archiveMounts.push(newMount)
            const node = fileInfoToNode(response.data.mountPoint)

            console.info('EMIT CREATED', node)
            emit('files:node:created', node)
          }
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
      this.setBusyState(false)
    },
    async unmount(mount) {
      console.info('UNMOUNT MOUNT', mount)
      const cloudUser = getCurrentUser()
      const url = generateRemoteUrl('dav/files/' + cloudUser.uid + mount.mountPointPath)
      this.setBusyState(true)
      try {
        await axios.delete(url)
        const mountIndex = this.archiveMounts.indexOf(mount)
        if (mountIndex >= 0) {
          this.archiveMounts.splice(mountIndex, 1)
        } else {
          console.error('UNABLE TO FIND DELETED MOUNT IN LIST', mount, this.archiveMounts)
        }
        const node = fileInfoToNode(mount.mountPoint)

        console.info('EMIT DELETED')
        emit('files:node:deleted', node)
      } catch (e) {
        console.error('ERROR', e)
        const messages = []
        if (e.response) {
          // attempt parsing Sabre exception is available
          const xml = e.response.request.responseXML
          if (xml && xml.documentElement.localName === 'error' && xml.documentElement.namespaceURI === 'DAV:') {
            const xmlMessages = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'message')
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
            this.refreshArchiveMounts()
          }
        }
      }
      this.setBusyState(false)
    },
    onNotification(event) {
      const destinationData = event?.notification?.messageRichParameters?.destination
      if (destinationData?.mount?.archiveFileId !== this.archiveFileId) {
        // not for us, in the future we may want to maintain a store
        // and cache the data for all file-ids.
        console.info('*** Archive notification for other file received', event)
        return
      }
      if (destinationData?.status === 'mount') {
        console.info('*** Mount notification received, updating mount-list', destinationData)
        const mountFileId = destinationData.id
        const mountIndex = this.archiveMounts.findIndex((mount) => mount.mountPoint.fileid === mountFileId)
        if (mountIndex === -1) {
          const mount = destinationData.mount
          mount.mountPoint = destinationData.folder
          this.archiveMounts.push(mount)
        }
      }
    },
    onMountPointRenamed(mountPoint) {
      // update the list of mountpoints
      const mountFileId = mountPoint.fileid
      const mountIndex = this.archiveMounts.findIndex((mount) => mount.mountPoint.fileid === mountFileId)
      if (mountIndex >= 0) {
        console.info('BERFORE RENAME', { ...this.archiveMounts[mountIndex] })
        vueSet(this.archiveMounts[mountIndex], 'mountPoint', mountPoint)
        vueSet(this.archiveMounts[mountIndex], 'mountPointPath', mountPoint.path)
        vueSet(this.archiveMounts[mountIndex], 'mountPointPathHash', md5(mountPoint.path))
        console.info('AFTER RENAME', { ...this.archiveMounts[mountIndex] })
      } else {
        console.info('RENAME OF NODE NOT FOR US', mountPoint)
      }
    },
    onMountPointDeleted(mountPoint) {
      const mountFileId = mountPoint.fileid
      const mountIndex = this.archiveMounts.findIndex((mount) => mount.mountPoint.fileid === mountFileId)
      if (mountIndex >= 0) {
        this.archiveMounts.splice(mountIndex, 1)
        console.info('RECORD UNMOUNT', mountPoint)
      } else {
        console.info('DELETE OF NODE NOT FOR US', mountPoint)
      }
    },
    async extractArchive() {
      const archivePath = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name)
      const targetPath = encodeURIComponent(this.archiveExtractPathName)
      const urlTemplate = this.archiveExtractBackgroundJob
        ? 'archive/schedule/extract/{archivePath}/{targetPath}'
        : 'archive/extract/{archivePath}/{targetPath}'
      const url = generateAppUrl(urlTemplate, { archivePath, targetPath })
      this.setBusyState(true)
      const requestData = {}
      if (this.archivePassPhrase) {
        requestData.passPhrase = this.archivePassPhrase
      }
      requestData.stripCommonPathPrefix = !!this.archiveExtractStripCommonPathPrefix
      try {
        const response = await axios.post(url, requestData)
        if (!this.archiveExtractBackgroundJob) {
          const node = fileInfoToNode(response.data.targetFolder)
          console.info('EMIT CREATED')
          emit('files:node:created', node)
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
            messages.push(t(appName, 'Archive extraction failed with error {status}, "{statusText}".', e.response))
          }
          for (const message of messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
          }
        }
      }
      this.setBusyState(false)
    },
    async setPassPhrase() {
      const newPassPhrase = this.showArchivePassPhrase
        ? this.$refs.archivePassPhrase.$el.querySelector('input[type="text"]').value
        : this.$refs.archivePassPhrase.$el.querySelector('input[type="password"]').value
      this.archivePassPhrase = newPassPhrase

      // patch it into existing mounts if any
      const archivePath = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name)
      const url = generateUrl('/apps/' + appName + '/archive/mount/{archivePath}', { archivePath })
      this.setBusyState(true)
      const requestData = {
        changeSet: {
          archivePassPhrase: this.archivePassPhrase,
        },
      }
      try {
        await axios.patch(url, requestData)
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
            messages.push(t(appName, 'Patching the passphrase failed with error {status}, "{statusText}".', e.response))
          }
          for (const message of messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
          }
        }
      }
      this.setBusyState(false)
    },
    async togglePassPhraseVisibility() {
      // this is sooo complicated because the NC NcAction controls are
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
