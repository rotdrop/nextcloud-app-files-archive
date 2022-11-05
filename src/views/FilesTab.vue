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
      <li class="files-tab-entry flex">
        <div class="files-tab-entry__avatar icon-info-white" />
        <div class="files-tab-entry__desc">
          <h5>{{ t(appName, 'Archive Information') }}</h5>
        </div>
        <Actions>
          <ActionButton v-model="showArchiveInfo"
                        :icon="'icon-triangle-' + (showArchiveInfo ? 's' : 'n')"
                        @click="showArchiveInfo = !showArchiveInfo"
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
      <li class="files-tab-entry flex">
        <div class="files-tab-entry__avatar icon-external-white" />
        <div class="files-tab-entry__desc">
          <h5>{{ t(appName, 'Mount Points') }}</h5>
        </div>
        <Actions>
          <ActionButton v-model="showArchiveMounts"
                        :icon="'icon-triangle-' + (showArchiveMounts ? 's' : 'n')"
                        @click="showArchiveMounts = !showArchiveMounts"
          />
        </Actions>
      </li>
      <li v-show="showArchiveMounts" class="files-tab-entry">
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
          NOT MOUNTED
        </div>
      </li>
      <li class="files-tab-entry flex">
        <div class="files-tab-entry__avatar icon-play-white" />
        <div class="files-tab-entry__desc">
          <h5>{{ t(appName, 'Extract Archive') }}</h5>
        </div>
      </li>
    </ul>
  </div>
</template>
<script>

import { appName } from '../config.js'
import { getInitialState } from '../services/InitialStateService.js'
import { generateUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import md5 from 'blueimp-md5'
import { showError, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import { formatFileSize } from '@nextcloud/files'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import axios from '@nextcloud/axios'

export default {
  name: 'FilesTab',
  components: {
    Actions,
    ActionButton,
    ListItem,
  },
  mixins: [
  ],
  data() {
    return {
      fileListElement: undefined,
      fileInfo: {},
      showArchiveInfo: true,
      showArchiveMounts: false,
      initialState: {},
      archiveInfo: {},
      archiveStatus: null,
      archiveMounts: [],
      archiveMounted: false,
      openMountTarget: md5(generateUrl('') + appName + '-open-archive-mount'),
      loading: 0,
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
      this.getData()
    },
    /**
     * Fetch some needed data ...
     */
    async getData() {
      this.initialState = getInitialState()

      this.fileListElement = document.querySelector('#fileList')
      const fileName = this.fileInfo.path + '/' + this.fileInfo.name
      this.getArchiveInfo(fileName)
      this.getArchiveMounts(fileName)
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
          for (const message of responseData.messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
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
          for (const message of responseData.messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
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
    async unmount(mount) {
      const fileListMount = this.fileListElement.querySelector('[data-path="' + mount.dirName + '"][data-file="' + mount.baseName + '"]')
      const cloudUser = getCurrentUser()
      const url = generateUrl('/remote.php/dav/files/' + cloudUser.uid + mount.mountPointPath)
      try {
        const response = await axios.delete(url)
        // FIXME: it would be good if a complete reload would not be neccessary
        window.location.reload()
      } catch (e) {
        console.error('ERROR', e)
        if (e.response && e.response.data) {
          const responseData = e.response.data
          for (const message of responseData.messages) {
            showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
          }
        }
      }
    },
  },
}
</script>
<style lang="scss" scoped>
.files-tab {
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
    &.flex {
      display:flex;
      align-items:center;
    }
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
  }
}
</style>
