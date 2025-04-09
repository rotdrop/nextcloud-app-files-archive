<!--
  - @author Claus-Justus Heine <himself@claus-justus-heine.de>
  - @copyright 2022, 2023, 2024, 2025 Claus-Justus Heine
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
          <NcListItem v-if="isLt(0, archiveStatus)"
                      :class="{ 'archive-error': isLt(0, archiveStatus) }"
                      :name="t(appName, 'archive status')"
                      :bold="true"
                      :details="archiveStatusText"
          >
            <template #icon>
              <div class="icon-error" />
            </template>
          </NcListItem>
          <NcListItem :name="t(appName, 'archive format')"
                      :bold="true"
                      :details="archiveInfo?.format || t(appName, 'unknown')"
                      compact
          />
          <NcListItem :name="t(appName, 'MIME type')"
                      :bold="true"
                      :details="archiveInfo?.mimeType || t(appName, 'unknown')"
                      compact
          />
          <NcListItem :name="t(appName, 'backend driver')"
                      :bold="true"
                      :details="archiveInfo?.backendDriver || t(appName, 'unknown')"
                      compact
          />
          <NcListItem :name="t(appName, 'uncompressed size')"
                      :bold="true"
                      :details="humanArchiveOriginalSize"
                      compact
          />
          <NcListItem :name="t(appName, 'compressed size')"
                      :bold="true"
                      :details="humanArchiveCompressedSize"
                      compact
          />
          <NcListItem v-if="humanArchiveCompressedSize !== humanArchiveFileSize"
                      :name="t(appName, 'archive file size')"
                      :bold="true"
                      :details="humanArchiveFileSize"
                      compact
          />
          <NcListItem :name="t(appName, '# archive members')"
                      :bold="true"
                      :details="numberOfArchiveMembers"
                      compact
          />
          <NcListItem :name="t(appName, 'common prefix')"
                      :bold="true"
                      :details="commonPathPrefix"
                      compact
          />
          <NcListItem v-if="archiveInfo?.comment"
                      class="archive-comment"
                      :name="t(appName, 'creator\'s comment')"
                      :bold="true"
                      compact
          >
            <template #subtitle>
              {{ archiveInfo?.comment }}
            </template>
          </NcListItem>
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
                         ref="archivePassPhraseComponent"
                         :value="archivePassPhrase"
                         type="text"
                         icon="icon-password"
                         @submit="setPassPhrase"
          >
            {{ t(appName, 'archive passphrase') }}
          </NcActionInput>
          <NcActionInput v-else
                         ref="archivePassPhraseComponent"
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
          <NcListItem v-for="mountPoint in archiveMounts"
                      :key="mountPoint.id"
                      :force-display-actions="true"
                      :bold="false"
          >
            <template #title>
              <a v-tooltip="mountPoint.mountPointPath"
                 class="external icon-folder icon"
                 :target="openMountTarget"
                 :href="filesAppMountPointUrl(mountPoint)"
              >
                {{ mountPoint.mountPointPath }}
              </a>
            </template>
            <template #actions>
              <NcActionButton @click="unmount(mountPoint)">
                <template #icon>
                  <NetworkOffIcon v-tooltip="t(appName, 'Disconnect storage')"
                                  :size="20"
                  />
                </template>
              </NcActionButton>
            </template>
            <template v-if="mountPoint.mountFlags & 1" #extra>
              <div>{{ t(appName, 'Common prefix {prefix} is stripped.', { prefix: commonPathPrefix }) }}</div>
            </template>
          </NcListItem>
        </ul>
        <div v-else>
          <FilePrefixPicker v-model="archiveMountFileInfo"
                            :hint="t(appName, 'Not mounted, create a new mount point:')"
                            :placeholder="t(appName, 'base name')"
                            @submit="mountArchive"
          />
          <div class="flex flex-center">
            <div class="label"
                 @click="openMountOptionsMenu"
            >
              {{ t(appName, 'Mount Options') }}
            </div>
            <NcActions ref="mountOptionsComponent"
                       :force-menu="true"
            >
              <NcActionCheckbox ref="mountStripCommonPathPrefixComponent"
                                :checked="archiveMountStripCommonPathPrefix"
                                @change="archiveMountStripCommonPathPrefix = !archiveMountStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </NcActionCheckbox>
              <NcActionCheckbox ref="mountBackgroundJobComponent"
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
                            @submit="extractArchive"
          />
          <div class="flex flex-center">
            <div class="label"
                 @click="openExtractionOptionsMenu"
            >
              {{ t(appName, 'Extraction Options') }}
            </div>
            <NcActions ref="extractionOptionsComponent"
                       :force-menu="true"
            >
              <NcActionCheckbox :checked="archiveExtractStripCommonPathPrefix"
                                @change="archiveExtractStripCommonPathPrefix = !archiveExtractStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </NcActionCheckbox>
              <NcActionCheckbox ref="extractBackgroundJobComponent"
                                :checked="archiveExtractBackgroundJob"
                                @change="archiveExtractBackgroundJob = !archiveExtractBackgroundJob"
              >
                {{ t(appName, 'schedule as background job') }}
              </NcActionCheckbox>
            </NcActions>
          </div>
        </div>
      </li>
      <li class="files-tab-entry flex flex-center clickable"
          @click="showPendingJobs = !showPendingJobs"
      >
        <div class="files-tab-entry__avatar icon-recent-white" />
        <div class="files-tab-entry__desc">
          <h5 v-if="jobsArePending">
            <span class="main-title">{{ t(appName, 'Pending Background Jobs') }}</span>
            <span v-if="jobsArePending" class="title-annotation">({{ '' + Object.keys(pendingJobs).length }})</span>
          </h5>
          <h5 v-else>
            <span class="main-title">{{ t(appName, 'No Pending Background Jobs') }}</span>
          </h5>
        </div>
        <NcActions>
          <NcActionButton v-model="showPendingJobs"
                          :icon="'icon-triangle-' + (showPendingJobs ? 'n' : 's')"
          />
        </NcActions>
      </li>
      <li v-show="jobsArePending && showPendingJobs" class="directory-chooser files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <ul v-else-if="jobsArePending" class="pending-jobs">
          <NcListItem v-for="job in pendingJobs"
                      :key="job.destinationPath"
                      :force-display-actions="true"
                      :bold="false"
          >
            <template #title>
              <div>{{ job.destinationPath }}</div>
            </template>
            <template #actions>
              <NcActionButton @click="cancelPendingOperation(job.target)">
                <template #icon>
                  <CancelIcon v-tooltip="t(appName, 'Cancel Job')"
                              :size="20"
                  />
                </template>
              </NcActionButton>
            </template>
            <template v-if="job.stripCommonPathPrefix" #extra>
              <div>{{ t(appName, 'Job type: {type}', {type: job.target === 'mount' ? t(appName, 'mount') : t(appName, 'extract')}) }}</div>
              <div>{{ t(appName, 'Common prefix {prefix} will be stripped.', { prefix: commonPathPrefix }) }}</div>
            </template>
          </NcListItem>
        </ul>
        <div v-else>
          {{ t(appName, 'No pending background job.') }}
        </div>
      </li>
    </ul>
  </div>
</template>
<script setup lang="ts">
import { appName } from '../config.ts'
import {
  computed,
  reactive,
  ref,
  set as vueSet,
  del as vueDel,
  onBeforeMount,
  onUnmounted,
  nextTick,
} from 'vue'
import getInitialState from '../toolkit/util/initial-state.ts'
import { generateUrl, generateRemoteUrl } from '@nextcloud/router'
import { emit, subscribe, unsubscribe } from '@nextcloud/event-bus'
import { getCurrentUser } from '@nextcloud/auth'
import generateAppUrl from '../toolkit/util/generate-url.ts'
import { fileInfoToNode } from '../toolkit/util/file-node-helper.ts'
import type { FileInfoDTO } from '../toolkit/util/file-node-helper.ts'
import { md5 } from 'js-md5'
import { showError, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs'
import { translate as t } from '@nextcloud/l10n'
import NetworkOffIcon from 'vue-material-design-icons/NetworkOff.vue'
import CancelIcon from 'vue-material-design-icons/Cancel.vue'
import { formatFileSize } from '@nextcloud/files'
import {
  NcActionInput,
  NcActionCheckbox,
  NcActions,
  NcActionButton,
  NcListItem,
} from '@nextcloud/vue'
import FilePrefixPicker from '@rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue'
import axios from '@nextcloud/axios'
import type { LegacyFileInfo, Node } from '@nextcloud/files'
import { isAxiosErrorResponse } from '../toolkit/types/axios-type-guards.ts'
import type { NextcloudEvents } from '@nextcloud/event-bus'
import type {
  ArchiveMount,
  ArchiveMountEntity,
  GetArchiveMountResponse,
} from '../model/archive-mount.d.ts'
import { setSidebarNodeBusy as setBusyState } from '../toolkit/util/nextcloud-sidebar-root.ts'
import logger from '../console.ts'
import type { InitialState } from '../types/initial-state.d.ts'

interface ArchiveInfo {
  commonPathPrefix: string,
  compressedSize: number,
  defaultMountPoint: string,
  defaultTargetBaseName: string,
  numberOfFiles: number,
  originalSize: number,
  size: number,
  format: string,
  mimeType: string,
  backendDriver: string,
  comment?: string,
}

interface ArchiveJob {
  target: 'mount'|'extract',
  userId: string,
  sourceId: number,
  sourcePath: string,
  destinationPath: string,
  archivePassphrase?: string,
  stripCommonPathPrefix: boolean,
  needsAuthentication: boolean,
  authToken: string,
}

setBusyState(false) // needs to be done once while in setup mode

const archivePassPhraseComponent = ref<null|typeof NcActionInput>(null)
const mountOptionsComponent = ref<null|typeof NcActions>(null)
const extractionOptionsComponent = ref<null|typeof NcActions>(null)

const loading = ref(0)

const fileInfo = ref<undefined|LegacyFileInfo>(undefined)
const fileName = computed(() => fileInfo.value ? fileInfo.value.path + '/' + fileInfo.value.name : null)
const archiveFileId = computed(() => fileInfo.value?.id)

const ArchiveStatusOk = 0
const ArchiveStatusTooLarge = 1
const ArchiveStatusBomb = 2

const archiveInfo = ref<undefined|ArchiveInfo>(undefined)
const archiveStatus = ref<undefined|number>(undefined)

const archiveMounts = ref<ArchiveMount[]>([])

const pendingJobs = ref<Record<string, ArchiveJob> >({})
const jobsArePending = computed(() => Object.keys(pendingJobs.value).length > 0)

const initialState = getInitialState<InitialState>()

const archiveMountStripCommonPathPrefix = ref(!!initialState?.mountStripCommonPathPrefixDefault)
const archiveExtractStripCommonPathPrefix = ref(!!initialState?.extractStripCommonPathPrefixDefault)
const archiveMountBackgroundJob = ref(!!initialState?.mountBackgroundJob)
const archiveExtractBackgroundJob = ref(!!initialState?.extractBackgroundJob)
const archivePassPhrase = ref<undefined|string>(undefined)

const showArchiveInfo = ref(true)
const showArchivePassPhrase = ref(false)
const showArchiveMounts = ref(false)
const showArchiveExtraction = ref(false)
const showPendingJobs = ref(false)
const openMountTarget = computed(() => md5(generateUrl('') + appName + '-open-archive-mount'))

const archiveMountFileInfo = reactive({
  dirName: '',
  baseName: '',
})

const archiveExtractFileInfo = reactive({
  dirName: '',
  baseName: '',
})

const archiveMountBaseName = computed({
  get() {
    return archiveMountFileInfo.baseName
  },
  set(value) {
    vueSet(archiveMountFileInfo, 'baseName', value)
    return value
  },
})

const archiveMountDirName = computed({
  get() {
    return archiveMountFileInfo.dirName
  },
  set(value) {
    vueSet(archiveMountFileInfo, 'dirName', value)
    return value
  },
})

const archiveExtractBaseName = computed({
  get() {
    return archiveExtractFileInfo.baseName
  },
  set(value) {
    vueSet(archiveExtractFileInfo, 'baseName', value)
    return value
  },
})

const archiveExtractDirName = computed({
  get() {
    return archiveExtractFileInfo.dirName
  },
  set(value) {
    vueSet(archiveExtractFileInfo, 'dirName', value)
    return value
  },
})

const archiveMountPathName = computed(() => archiveMountDirName.value + (archiveMountBaseName.value ? '/' + archiveMountBaseName.value : ''))
const archiveExtractPathName = computed(() => archiveExtractDirName.value + (archiveExtractBaseName.value ? '/' + archiveExtractBaseName.value : ''))

const archiveStatusText = computed(() => {
  if (archiveStatus.value! === ArchiveStatusOk) {
    return t(appName, 'ok')
  } else if (archiveStatus.value! & ArchiveStatusBomb) {
    return t(appName, 'zip bomb')
  } else if (archiveStatus.value! & ArchiveStatusTooLarge) {
    return t(appName, 'too large')
  }
  return t(appName, 'unknown')
})

// const archiveFileDirName = computed(() => fileInfo.value?.path)
// const archiveFileBaseName = computed(() => fileInfo.value?.name)
// const archiveFilePathName = computed(() => fileInfo.value?.path + '/' + fileInfo.value?.name)
const archiveMounted = computed(() => archiveMounts.value.length > 0)
// const archiveInfoText = computed(() => JSON.stringify(archiveInfo.value, null, 2))
const humanArchiveOriginalSize = computed(() =>
  !isNaN(parseInt('' + archiveInfo.value?.originalSize))
    ? formatFileSize(archiveInfo.value!.originalSize)
    : t(appName, 'unknown'),
)
const humanArchiveCompressedSize = computed(() =>
  !isNaN(parseInt('' + archiveInfo.value?.compressedSize))
    ? formatFileSize(archiveInfo.value!.compressedSize)
    : t(appName, 'unknown'),
)
const humanArchiveFileSize = computed(() =>
  !isNaN(parseInt('' + archiveInfo.value?.size))
    ? formatFileSize(archiveInfo.value!.size)
    : t(appName, 'unknown'),
)
const numberOfArchiveMembers = computed(() => {
  if (!archiveInfo.value
      || archiveInfo.value?.numberOfFiles === undefined
      || isNaN(parseInt('' + archiveInfo.value?.numberOfFiles))) {
    return t(appName, 'unknown')
  }
  return '' + archiveInfo.value?.numberOfFiles
})
const commonPathPrefix = computed(() =>
  !archiveInfo.value
    || archiveInfo.value.commonPathPrefix === undefined
    ? t(appName, 'unknown')
    : '/' + archiveInfo.value.commonPathPrefix,
)
// const mountPointTitle = computed(() =>
//   t(appName, 'Mount Points')
//     + ' ('
//     + (archiveMounted ? archiveMounts.value.length : t(appName, 'not mounted'))
//     + ')'
// )

// We ____DO____  want to compare numerically here.
const isLt = (a: null|undefined|number, b: null|undefined|number) => a! < b!

const openMountOptionsMenu = () => {
  mountOptionsComponent.value?.openMenu()
}

const openExtractionOptionsMenu = () => {
  extractionOptionsComponent.value?.openMenu()
}

/**
 * Fetch some needed data ...
 */
const getData = async () => {
  archiveMountStripCommonPathPrefix.value = !!initialState?.mountStripCommonPathPrefixDefault
  archiveExtractStripCommonPathPrefix.value = !!initialState?.extractStripCommonPathPrefixDefault
  archiveMountBackgroundJob.value = !!initialState?.mountBackgroundJob
  archiveExtractBackgroundJob.value = !!initialState?.extractBackgroundJob

  if (!fileName.value) {
    return
  }

  getArchiveInfo(fileName.value)
  refreshArchiveMounts(fileName.value, true)
  getPendingJobs(fileName.value, true)
}

/**
 * Update current fileInfo and fetch new data
 * @param newFileInfo the current file FileInfo
 */
const update = async (newFileInfo: LegacyFileInfo) => {
  fileInfo.value = newFileInfo

  /* this.fileList = OCA.Files.App.currentFileList
   * this.fileList.$el.off('updated').on('updated', function(event) {
   *   logger.info('FILE LIST UPDATED, ARGS', arguments)
   * }) */
  archiveMountBaseName.value = fileInfo.value.name.split('.')[0]
  archiveMountDirName.value = fileInfo.value.path

  archiveExtractBaseName.value = archiveMountBaseName.value
  archiveExtractDirName.value = archiveMountDirName.value

  getData()
}

defineExpose({ update })

interface ArchiveInfoResponse {
  messages: string[],
  archiveStatus: number, //
  archiveInfo: ArchiveInfo,
}

const getArchiveInfo = async (fileName: string) => {
  ++loading.value
  fileName = encodeURIComponent(fileName)
  const url = generateAppUrl('archive/info/{fileName}', { fileName })
  const requestData: Record<string, string> = {}
  if (archivePassPhrase.value) {
    requestData.passPhrase = archivePassPhrase.value
  }
  try {
    const response = await axios.post<ArchiveInfoResponse>(url, requestData)
    const responseData = response.data
    archiveInfo.value = responseData.archiveInfo
    archiveStatus.value = responseData.archiveStatus
    if (responseData.messages) {
      for (const message of responseData.messages) {
        showInfo(message)
      }
    }
  } catch (e) {
    logger.error('ERROR', e)
    if (isAxiosErrorResponse(e) && e.response.data) {
      const responseData = e.response.data as ArchiveInfoResponse
      archiveInfo.value = responseData.archiveInfo
      archiveStatus.value = responseData.archiveStatus
      if (responseData.messages) {
        for (const message of responseData.messages) {
          showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
        }
      }
    } else {
      archiveInfo.value = undefined
    }
  }
  if (archiveInfo.value?.defaultMountPoint) {
    archiveMountBaseName.value = archiveInfo.value.defaultMountPoint
  }
  if (archiveInfo.value?.defaultTargetBaseName) {
    archiveExtractBaseName.value = archiveInfo.value.defaultTargetBaseName
  }
  --loading.value
}

const refreshArchiveMounts = async (filename: string, noEmit?: boolean) => {
  const oldMounts = [...archiveMounts.value]
  const mounts = await getArchiveMounts(filename, false)
  archiveMounts.value = mounts.mounts
  if (noEmit) {
    // do no emit birth during initialization
    return
  }
  // emit birth and death signals as needed, in order to update
  // the frontend file-listing. The computational effort is
  // quadratic, but we are talking here about the common case that
  // there is only a single mount -- or by accident another
  // one. So what.
  const newMounts = archiveMounts.value.filter((mount) => oldMounts.findIndex((oldMount) => mount.mountPoint.fileid === oldMount.mountPoint.fileid) === -1)
  const deletedMounts = oldMounts.filter((oldMount) => archiveMounts.value.findIndex((mount) => mount.mountPoint.fileid === oldMount.mountPoint.fileid) === -1)
  for (const mount of deletedMounts) {
    const node = fileInfoToNode(mount.mountPoint)
    node.attributes['is-mount-root'] = true

    emit('files:node:deleted', node)
  }
  for (const mount of newMounts) {
    const node = fileInfoToNode(mount.mountPoint)
    node.attributes['is-mount-root'] = true

    emit('files:node:created', node)
  }
}

const getJobIdFromOperation = (operation: string, archivePath: string, mountPath: string) => {
  return md5(operation + archivePath + mountPath)
}

const getJobIdFromJob = (job: ArchiveJob) => {
  return getJobIdFromOperation(job.target, job.sourcePath, job.destinationPath)
}

const getPendingJobs = async (fileName: string, silent?: boolean) => {
  if (silent !== true) {
    ++loading.value
  }
  fileName = encodeURIComponent(fileName)
  const url = generateAppUrl('archive/schedule/{operation}/{fileName}', { operation: 'status', fileName })
  try {
    const response = await axios.get<ArchiveJob[]>(url)
    const responseData = response.data
    const jobs = {}
    for (const job of responseData) {
      jobs[getJobIdFromJob(job)] = job
    }
    for (const jobId of Object.keys(pendingJobs.value)) {
      if (!jobs[jobId]) {
        vueDel(pendingJobs.value, jobId)
      }
    }
    for (const [jobId, job] of Object.entries(jobs)) {
      vueSet(pendingJobs.value, jobId, job)
    }
  } catch (e) {
    logger.error('ERROR', e)
    if (isAxiosErrorResponse(e) && e.response.data) {
      const responseData = e.response.data as { messages?: string[] }
      if (responseData.messages) {
        for (const message of responseData.messages) {
          showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
        }
      }
    }
  }
  if (silent !== true) {
    --loading.value
  }
}

interface CancelJobResponse {
  removed?: ArchiveJob[],
  messages?: string[],
}

const cancelPendingOperation = async (operation: 'extract'|'mount') => {
  const archivePath = encodeURIComponent(fileName.value!)
  const mountPath = encodeURIComponent(archiveMountPathName.value)
  const url = generateAppUrl(
    'archive/schedule/{operation}/{archivePath}/{mountPath}',
    {
      operation,
      archivePath,
      mountPath,
    },
  )
  let responseData: CancelJobResponse = {}
  try {
    const response = await axios.delete<CancelJobResponse>(url, {})
    responseData = response.data
  } catch (e) {
    logger.error('ERROR', e)
    if (isAxiosErrorResponse(e)) {
      const messages: string[] = []
      if (e.response.data) {
        responseData = e.response.data as CancelJobResponse
        if (Array.isArray(responseData.messages)) {
          messages.splice(messages.length, 0, ...responseData.messages)
        }
      }
      if (!messages.length) {
        messages.push(
          t(appName, 'Cancelling the background job failed with error {status}, "{statusText}".', {
            status: e.response.status,
            statusText: e.response.statusText,
          }),
        )
      }
      for (const message of messages) {
        showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
      }
    }
  }
  if (responseData.removed) {
    for (const job of responseData.removed) {
      const jobId = getJobIdFromJob(job)
      if (pendingJobs.value[jobId]) {
        vueDel(pendingJobs.value, jobId)
      }
    }
  }
}

const getArchiveMounts = async (fileName: string, silent?: boolean) => {
  const result: Omit<GetArchiveMountResponse, 'messages'> = {
    mounts: [],
    mounted: false,
  }
  if (silent !== true) {
    ++loading.value
  }
  fileName = encodeURIComponent(fileName)
  const url = generateAppUrl('archive/mount/{fileName}', { fileName })
  try {
    const response = await axios.get<GetArchiveMountResponse>(url)
    const responseData = response.data
    result.mounts = responseData.mounts
    result.mounted = responseData.mounted
    if (responseData.messages) {
      for (const message of responseData.messages) {
        showInfo(message)
      }
    }
  } catch (e) {
    logger.error('ERROR', e)
    if (isAxiosErrorResponse(e) && e.response.data) {
      const responseData = e.response.data as GetArchiveMountResponse
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
    // const pathComponents = mount.mountPointPath.split('/')
    // const baseName = pathComponents.pop()
    // const dirName = pathComponents.join('/')
    // mount.baseName = baseName
    // mount.dirName = dirName
    if (!archivePassPhrase.value && mount.archivePassPhrase) {
      archivePassPhrase.value = mount.archivePassPhrase
    }
    delete mount.archivePassPhrase
  }
  if (silent !== true) {
    --loading.value
  }
  return result
}

const mountArchive = async () => {
  const archivePath = encodeURIComponent(fileName.value!)
  const mountPath = encodeURIComponent(archiveMountPathName.value)
  const urlTemplate = archiveMountBackgroundJob.value
    ? 'archive/schedule/mount/{archivePath}/{mountPath}'
    : 'archive/mount/{archivePath}/{mountPath}'
  const url = generateAppUrl(urlTemplate, { archivePath, mountPath })
  setBusyState(true)
  const requestData: Record<string, string|boolean> = {}
  if (archivePassPhrase.value) {
    requestData.passPhrase = archivePassPhrase.value
  }
  requestData.stripCommonPathPrefix = !!archiveMountStripCommonPathPrefix.value
  try {
    const response = await axios.post<ArchiveMount>(url, requestData)
    if (!archiveMountBackgroundJob.value) {
      const newMount = response.data
      const newFileId = newMount.mountPoint.fileid
      if (archiveMounts.value.findIndex((mount) => mount.mountPoint.fileid === newFileId) === -1) {
        archiveMounts.value.push(newMount)
        const node = fileInfoToNode(response.data.mountPoint)
        node.attributes['is-mount-root'] = true

        emit('files:node:created', node)
      }
    }
  } catch (e) {
    logger.error('ERROR', e)
    if (isAxiosErrorResponse(e)) {
      const messages: string[] = []
      if (e.response.data) {
        const responseData = e.response.data as { messages?: string[] }
        if (responseData.messages) {
          messages.splice(messages.length, 0, ...responseData.messages)
        }
      }
      if (!messages.length) {
        messages.push(t(appName, 'Mount request failed with error {status}, "{statusText}".', {
          status: e.response.status,
          statusText: e.response.statusText,
        }))
      }
      for (const message of messages) {
        showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
      }
    }
  }
  if (archiveMountBackgroundJob.value) {
    getPendingJobs(fileName.value!, true)
  }
  setBusyState(false)
}

const unmount = async (mount: ArchiveMount) => {
  const cloudUser = getCurrentUser()
  const url = generateRemoteUrl('dav/files/' + cloudUser!.uid + mount.mountPointPath)
  setBusyState(true)
  try {
    await axios.delete(url)
    const mountIndex = archiveMounts.value.indexOf(mount)
    if (mountIndex >= 0) {
      archiveMounts.value.splice(mountIndex, 1)
    } else {
      logger.error('UNABLE TO FIND DELETED MOUNT IN LIST', mount, archiveMounts)
    }
    const node = fileInfoToNode(mount.mountPoint)
    node.attributes['is-mount-root'] = true

    emit('files:node:deleted', node)
  } catch (e) {
    logger.error('ERROR', e)
    const messages: string[] = []
    if (isAxiosErrorResponse(e)) {
      // attempt parsing Sabre exception is available
      const xml = (e.response.request as XMLHttpRequest)?.responseXML
      if (xml && xml.documentElement.localName === 'error' && xml.documentElement.namespaceURI === 'DAV:') {
        const xmlMessages = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'message')
        // const exceptions = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'exception');
        for (const message of xmlMessages) {
          message.textContent && messages.push(message.textContent)
        }
      }
      if (e.response.data) {
        const responseData = e.response.data as { messages?: string[] }
        if (responseData.messages) {
          messages.splice(messages.length, 0, ...responseData.messages)
        }
      }
      if (!messages.length) {
        messages.push(t(appName, 'Unmount request failed with error {status}, "{statusText}".', {
          status: e.response.status,
          statusText: e.response.statusText,
        }))
      }
      for (const message of messages) {
        showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
      }
      if (e.response.status === 404) {
        refreshArchiveMounts(fileName.value!, true)
      }
    }
  }
  setBusyState(false)
}

const extractArchive = async () => {
  const archivePath = encodeURIComponent(fileName.value!)
  const targetPath = encodeURIComponent(archiveExtractPathName.value)
  const urlTemplate = archiveExtractBackgroundJob.value
    ? 'archive/schedule/extract/{archivePath}/{targetPath}'
    : 'archive/extract/{archivePath}/{targetPath}'
  const url = generateAppUrl(urlTemplate, { archivePath, targetPath })
  setBusyState(true)
  const requestData: Record<string, string|boolean> = {}
  if (archivePassPhrase.value) {
    requestData.passPhrase = archivePassPhrase.value
  }
  requestData.stripCommonPathPrefix = !!archiveExtractStripCommonPathPrefix.value
  try {
    const response = await axios.post<{ targetFolder: FileInfoDTO }>(url, requestData)
    if (!archiveExtractBackgroundJob.value) {
      const node = fileInfoToNode(response.data.targetFolder)
      node.attributes['is-mount-root'] = true

      emit('files:node:created', node)
    }
  } catch (e) {
    logger.error('ERROR', e)
    if (isAxiosErrorResponse(e)) {
      const messages: string[] = []
      if (e.response.data) {
        const responseData = e.response.data as { messages?: string[] }
        if (responseData.messages) {
          messages.splice(messages.length, 0, ...responseData.messages)
        }
      }
      if (!messages.length) {
        messages.push(t(appName, 'Archive extraction failed with error {status}, "{statusText}".', {
          status: e.response.status,
          statusText: e.response.statusText,
        }))
      }
      for (const message of messages) {
        showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
      }
    }
  }
  if (archiveExtractBackgroundJob.value) {
    getPendingJobs(fileName.value!, true)
  }
  setBusyState(false)
}

const setPassPhrase = async () => {
  const newPassPhrase: string = showArchivePassPhrase.value
    ? archivePassPhraseComponent.value!.$el.querySelector<HTMLInputElement>('input[type="text"]')?.value || ''
    : archivePassPhraseComponent.value!.$el.querySelector<HTMLInputElement>('input[type="password"]')?.value || ''
  archivePassPhrase.value = newPassPhrase

  // patch it into existing mounts if any
  const archivePath = encodeURIComponent(fileName.value!)
  const url = generateUrl('/apps/' + appName + '/archive/mount/{archivePath}', { archivePath })
  setBusyState(true)
  const requestData = {
    changeSet: {
      archivePassPhrase: archivePassPhrase.value,
    },
  }
  try {
    await axios.patch(url, requestData)
  } catch (e) {
    logger.error('ERROR', e)
    if (isAxiosErrorResponse(e)) {
      const messages: string[] = []
      if (e.response.data) {
        const responseData = e.response.data as { messages?: string[] }
        if (responseData.messages) {
          messages.splice(messages.length, 0, ...responseData.messages)
        }
      }
      if (!messages.length) {
        messages.push(t(appName, 'Patching the passphrase failed with error {status}, "{statusText}".', {
          status: e.response.status,
          statusText: e.response.statusText,
        }))
      }
      for (const message of messages) {
        showError(message, { timeout: TOAST_PERMANENT_TIMEOUT })
      }
    }
  }
  setBusyState(false)
}

const togglePassPhraseVisibility = async () => {
  // this is sooo complicated because the NC NcAction controls are
  // seemingly only pro-forma vue-controls. There is no working
  // v-model support, e.g.
  let visibleElement = showArchivePassPhrase.value
    ? archivePassPhraseComponent.value!.$el.querySelector<HTMLInputElement>('input[type="text"]')!
    : archivePassPhraseComponent.value!.$el.querySelector<HTMLInputElement>('input[type="password"]')!
  const currentValue: string = visibleElement.value

  showArchivePassPhrase.value = !showArchivePassPhrase.value
  await nextTick()

  visibleElement = showArchivePassPhrase.value
    ? archivePassPhraseComponent.value!.$el.querySelector<HTMLInputElement>('input[type="text"]')!
    : archivePassPhraseComponent.value!.$el.querySelector<HTMLInputElement>('input[type="password"]')!
  visibleElement.value = currentValue
}

const filesAppMountPointUrl = (mountPoint: ArchiveMount) => {
  return generateUrl('/apps/files') + '?dir=' + encodeURIComponent(mountPoint.mountPointPath)
}

const onNotification = (event: NextcloudEvents['notifications:notification:received']) => {
  if (event?.notification?.app !== appName) {
    return
  }
  const destinationData = event?.notification?.messageRichParameters?.destination
  if (destinationData?.status !== 'mount') {
    return // nothing special ATM
  }
  const mountData = destinationData?.mount
  if (!mountData) {
    logger.error('No mount info in mount notification event')
    return
  }
  let mount: ArchiveMountEntity
  try {
    mount = JSON.parse(mountData)
  } catch (error) {
    logger.error('files_archive, unable to decode mount entity', { event, mountData })
    return
  }
  if (mount.archiveFileId !== archiveFileId.value) {
    // not for us, in the future we may want to maintain a store
    // and cache the data for all file-ids.
    logger.info('*** Archive notification for other file received', event)
    return
  }
  const jobId = getJobIdFromOperation('mount', mount.archiveFilePath, mount.mountPointPath)
  if (pendingJobs.value[jobId]) {
    delete pendingJobs.value[jobId]
  }
  logger.info('*** Mount notification received, updating mount-list', destinationData)
  const mountFileId = destinationData.id
  const mountIndex = archiveMounts.value.findIndex((mount) => mount.mountPoint.fileid === mountFileId)
  if (mountIndex === -1) {
    try {
      archiveMounts.value.push({ ...mount, mountPoint: JSON.parse(destinationData.folder) })
    } catch (error) {
      logger.error('Unable to decode mount point folder file-info record.', { destinationData })
    }
  }
}

const onMountPointRenamed = (mountPoint: Node) => {
  // update the list of mountpoints
  const mountFileId = mountPoint.fileid
  const mountIndex = archiveMounts.value.findIndex((mount) => mount.mountPoint.fileid === mountFileId)
  if (mountIndex >= 0) {
    logger.info('BERFORE RENAME', { ...archiveMounts[mountIndex] })
    vueSet(archiveMounts[mountIndex], 'mountPoint', mountPoint)
    vueSet(archiveMounts[mountIndex], 'mountPointPath', mountPoint.path)
    vueSet(archiveMounts[mountIndex], 'mountPointPathHash', md5(mountPoint.path))
    logger.info('AFTER RENAME', { ...archiveMounts[mountIndex] })
  } else {
    logger.info('RENAME OF NODE NOT FOR US', mountPoint)
  }
}

const onMountPointDeleted = (mountPoint: Node) => {
  const mountFileId = mountPoint.fileid
  const mountIndex = archiveMounts.value.findIndex((mount) => mount.mountPoint.fileid === mountFileId)
  if (mountIndex >= 0) {
    archiveMounts.value.splice(mountIndex, 1)
    logger.info('RECORD UNMOUNT', mountPoint)
  } else {
    logger.info('DELETE OF NODE NOT FOR US', {
      mountPoint,
      archiveMounts: archiveMounts.value,
    })
  }
}

onBeforeMount(() => {
  subscribe('files:node:deleted', onMountPointDeleted)
  subscribe('files:node:renamed', onMountPointRenamed)
  subscribe('notifications:notification:received', onNotification)
})

onUnmounted(() => {
  unsubscribe('files:node:deleted', onMountPointDeleted)
  unsubscribe('files:node:renamed', onMountPointRenamed)
  unsubscribe('notifications:notification:received', onNotification)
})

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
