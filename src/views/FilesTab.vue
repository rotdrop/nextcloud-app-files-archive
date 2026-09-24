<!--
  - @author Claus-Justus Heine <himself@claus-justus-heine.de>
  - @copyright 2022-2026 Claus-Justus Heine
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
                          @click.stop=""
          />
        </NcActions>
      </li>
      <li v-show="showArchiveInfo" class="files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <ul v-show="!loading" class="archive-info">
          <NcListItem v-if="archiveError"
                      :class="{ 'archive-error': archiveError }"
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
                      compact
          >
            <template #subname>
              <div v-tooltip="commonPathPrefix">
                {{ commonPathPrefix }}
              </div>
            </template>
          </NcListItem>
          <NcListItem v-if="archiveInfo?.comment"
                      class="archive-comment"
                      :name="t(appName, 'creator\'s comment')"
                      :bold="true"
                      compact
          >
            <template #subname>
              <div v-tooltip="archiveInfo?.comment">
                {{ archiveInfo?.comment }}
              </div>
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
        <NcActions :forceMenu="true">
          <NcActionInput v-model="archivePassPhrase"
                         type="password"
                         icon="icon-password"
                         @submit="setPassPhrase"
          >
            {{ t(appName, 'archive passphrase') }}
          </NcActionInput>
        </NcActions>
      </li>
      <li v-if="!archiveMountDisabled || archiveMounted"
          class="files-tab-entry flex flex-center clickable"
          @click="showArchiveMounts = !showArchiveMounts"
      >
        <div class="files-tab-entry__avatar icon-external-white" />
        <div class="files-tab-entry__desc">
          <h5>
            <span class="main-title">{{ n(appName, 'Mount Point', 'Mount Points', archiveMounts.length) }}</span>
            <span v-if="archiveMounted" class="title-annotation">({{ `${archiveMounts.length}` }})</span>
            <span v-else class="title-annotation">({{ t(appName, 'not mounted') }})</span>
          </h5>
        </div>
        <NcActions>
          <NcActionButton v-model="showArchiveMounts"
                          :icon="'icon-triangle-' + (showArchiveMounts ? 'n' : 's')"
                          @click.stop=""
          />
        </NcActions>
      </li>
      <li v-if="!archiveMountDisabled || archiveMounted"
          v-show="showArchiveMounts"
          class="directory-chooser files-tab-entry"
      >
        <div v-if="loading" class="icon-loading-small" />
        <ul v-else-if="archiveMounted" class="archive-mounts">
          <NcListItem v-for="mountPoint in archiveMounts"
                      :key="mountPoint.id"
                      :forceDisplayActions="true"
                      :bold="false"
          >
            <template #name>
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
            <template v-if="mountPoint.mountFlags & 1" #subname>
              <div>{{ t(appName, 'Common prefix {prefix} is stripped.', { prefix: commonPathPrefix }) }}</div>
            </template>
          </NcListItem>
        </ul>
        <div v-else-if="!archiveMountDisabled">
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
                       :forceMenu="true"
            >
              <NcActionCheckbox v-model="archiveMountStripCommonPathPrefix"
                                @change="archiveMountStripCommonPathPrefix = !archiveMountStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </NcActionCheckbox>
              <NcActionCheckbox v-model="archiveMountBackgroundJob"
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
                          @click.stop=""
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
                       :forceMenu="true"
            >
              <NcActionCheckbox v-model="archiveExtractStripCommonPathPrefix"
                                @change="archiveExtractStripCommonPathPrefix = !archiveExtractStripCommonPathPrefix"
              >
                {{ t(appName, 'strip common path prefix') }}
              </NcActionCheckbox>
              <NcActionCheckbox v-model="archiveExtractBackgroundJob"
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
                          @click.stop=""
          />
        </NcActions>
      </li>
      <li v-show="jobsArePending && showPendingJobs" class="directory-chooser files-tab-entry">
        <div v-if="loading" class="icon-loading-small" />
        <ul v-else-if="jobsArePending" class="pending-jobs">
          <NcListItem v-for="job in pendingJobs"
                      :key="job.destinationPath"
                      :forceDisplayActions="true"
                      :bold="false"
          >
            <template #name>
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
import type { NextcloudEvents } from '@nextcloud/event-bus'
import type {
  IFolder,
  INode,
  // IView,
} from '@nextcloud/files'
import type {
  ArchiveMount,
  ArchiveMountDTO,
  ArchiveMountEntity,
  GetArchiveMountResponse,
} from '../model/archive-mount.d.ts'
import type { FileInfoDTO } from '../toolkit/util/file-node-helper.ts'
import type { InitialState } from '../types/initial-state.d.ts'
import type { DestinationParameter } from '../types/notification.d.ts'

import { getCurrentUser } from '@nextcloud/auth'
import axios from '@nextcloud/axios'
import { emit, subscribe, unsubscribe } from '@nextcloud/event-bus'
import { formatFileSize } from '@nextcloud/files'
import {
  translatePlural as n,
  translate as t,
} from '@nextcloud/l10n'
import { generateRemoteUrl, generateUrl } from '@nextcloud/router'
import {
  NcActionButton,
  NcActionCheckbox,
  NcActionInput,
  NcActions,
  NcListItem,
} from '@nextcloud/vue'
import vTooltip from '@rotdrop/nextcloud-vue-components/lib/directives/Tooltip'
import { md5 } from 'js-md5'
import {
  computed,
  onBeforeMount,
  onUnmounted,
  ref,
  useTemplateRef,
  // watch,
} from 'vue'
import FilePrefixPicker from '@rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue'
import CancelIcon from 'vue-material-design-icons/Cancel.vue'
import NetworkOffIcon from 'vue-material-design-icons/NetworkOff.vue'
import { appName } from '../config.ts'
import logger from '../console.ts'
import { isAxiosErrorResponse } from '../toolkit/types/axios-type-guards.ts'
import { setFileNodeBusy } from '../toolkit/util/file-node-busy-indicator.ts'
import { fileInfoToNode } from '../toolkit/util/file-node-helper.ts'
import generateAppUrl from '../toolkit/util/generate-url.ts'
import getInitialState from '../toolkit/util/initial-state.ts'
import { showError, showInfo, TOAST_PERMANENT_TIMEOUT } from '../toolkit/util/toasts.ts'

interface ArchiveInfo {
  commonPathPrefix: string
  compressedSize: number
  defaultMountPoint: string
  defaultTargetBaseName: string
  numberOfFiles: number
  originalSize: number
  size: number
  format: string
  mimeType: string
  backendDriver: string
  comment?: string
}

interface ArchiveJob {
  target: 'mount'|'extract'
  userId: string
  sourceId: number
  sourcePath: string
  destinationPath: string
  archivePassphrase?: string
  stripCommonPathPrefix: boolean
  needsAuthentication: boolean
  authToken: string
}

const props = withDefaults(defineProps<{
  node: INode
  // folder?: IFolder
  // view?: IView
}>(), {
  // folder: undefined,
  // view: undefined,
})

logger.info(
  'PROPS',
  {
    props,
    node: {
      path: props.node.path,
      basename: props.node.basename,
      dirname: props.node.dirname,
    },
  },
)

const setBusyState = (state: boolean) => {
  setFileNodeBusy(props.node, state)
}

setBusyState(false) // needs to be done once while in setup mode

const mountOptionsComponent = useTemplateRef<typeof NcActions>('mountOptionsComponent')
const extractionOptionsComponent = useTemplateRef<typeof NcActions>('extractionOptionsComponent')

const loading = ref(0)

const fileName = computed(() => props.node ? props.node.path : null)
const archiveFileId = computed(() => props.node?.id)

const ArchiveStatusOk = 0
const ArchiveStatusTooLarge = 1
const ArchiveStatusBomb = 2

const archiveInfo = ref<undefined|ArchiveInfo>(undefined)
const archiveStatus = ref<undefined|number>(undefined)
const archiveError = computed(() => 0 < archiveStatus.value!)

const archiveMounts = ref<ArchiveMount[]>([])

const pendingJobs = ref<Record<string, ArchiveJob>>({})
const jobsArePending = computed(() => Object.keys(pendingJobs.value).length > 0)

const initialState = getInitialState<InitialState>()

const archiveMountStripCommonPathPrefix = ref(!!initialState?.mountStripCommonPathPrefixDefault)
const archiveExtractStripCommonPathPrefix = ref(!!initialState?.extractStripCommonPathPrefixDefault)
const archiveMountBackgroundJob = ref(!!initialState?.mountBackgroundJob)
const archiveExtractBackgroundJob = ref(!!initialState?.extractBackgroundJob)
const archiveMountDisabled = !!initialState?.mountDisabled
const archivePassPhrase = ref<undefined|string>(undefined)

const showArchiveInfo = ref(true)
const showArchiveMounts = ref(false)
const showArchiveExtraction = ref(false)
const showPendingJobs = ref(false)
const openMountTarget = computed(() => md5(generateUrl('') + appName + '-open-archive-mount'))

const archiveMountFileInfo = ref({
  dirName: '',
  baseName: '',
})

const archiveExtractFileInfo = ref({
  dirName: '',
  baseName: '',
})

const archiveMountBaseName = computed({
  get() {
    return archiveMountFileInfo.value.baseName
  },
  set(value) {
    archiveMountFileInfo.value.baseName = value
    return value
  },
})

const archiveMountDirName = computed({
  get() {
    return archiveMountFileInfo.value.dirName
  },
  set(value) {
    archiveMountFileInfo.value.dirName = value
    return value
  },
})

const archiveExtractBaseName = computed({
  get() {
    return archiveExtractFileInfo.value.baseName
  },
  set(value) {
    archiveExtractFileInfo.value.baseName = value
    return value
  },
})

const archiveExtractDirName = computed({
  get() {
    return archiveExtractFileInfo.value.dirName
  },
  set(value) {
    archiveExtractFileInfo.value.dirName = value
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

const archiveMounted = computed(() => archiveMounts.value.length > 0)
// const archiveInfoText = computed(() => JSON.stringify(archiveInfo.value, null, 2))
const humanArchiveOriginalSize = computed(
  () => !isNaN(parseInt('' + archiveInfo.value?.originalSize))
    ? formatFileSize(archiveInfo.value!.originalSize)
    : t(appName, 'unknown'),
)
const humanArchiveCompressedSize = computed(
  () => !isNaN(parseInt('' + archiveInfo.value?.compressedSize))
    ? formatFileSize(archiveInfo.value!.compressedSize)
    : t(appName, 'unknown'),
)
const humanArchiveFileSize = computed(
  () => !isNaN(parseInt('' + archiveInfo.value?.size))
    ? formatFileSize(archiveInfo.value!.size)
    : t(appName, 'unknown'),
)
const numberOfArchiveMembers = computed(
  () => {
    if (!archiveInfo.value
      || archiveInfo.value?.numberOfFiles === undefined
      || isNaN(parseInt('' + archiveInfo.value?.numberOfFiles))) {
      return t(appName, 'unknown')
    }
    return '' + archiveInfo.value?.numberOfFiles
  },
)
const commonPathPrefix = computed(
  () => !archiveInfo.value
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
// const isLt = (a: null|undefined|number, b: null|undefined|number) => a! < b!

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

// watch(
//   () => props.node,
//   async () => {
//     logger.debug('Node has changed', {
//       node: { ...props.node },
//       folder: { ...props.folder },
//       view: { ...props.view },
//     })
//     await update()
//   },
//   { immediate: true },
// )

/**
 * Update current fileInfo and fetch new data.
 */
async function update() {
  /* this.fileList = OCA.Files.App.currentFileList
   * this.fileList.$el.off('updated').on('updated', function(event) {
   *   logger.info('FILE LIST UPDATED, ARGS', arguments)
   * }) */
  archiveMountBaseName.value = props.node.basename.split('.')[0]
  archiveMountDirName.value = props.node.dirname

  archiveExtractBaseName.value = archiveMountBaseName.value
  archiveExtractDirName.value = archiveMountDirName.value

  getData()
}

interface ArchiveInfoResponse {
  messages: string[]
  archiveStatus: number
  archiveInfo: ArchiveInfo
}

/**
 * @param fileName TBD.
 */
async function getArchiveInfo(fileName: string) {
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
    logger.trace('ERROR', e)
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

/**
 * @param filename TBD.
 *
 * @param noEmit TBD.
 */
async function refreshArchiveMounts(filename: string, noEmit?: boolean) {
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
  const newMounts = archiveMounts.value.filter((mount) => oldMounts.findIndex((oldMount) => mount.mountPoint.id === oldMount.mountPoint.id) === -1)
  const deletedMounts = oldMounts.filter((oldMount) => archiveMounts.value.findIndex((mount) => mount.mountPoint.id === oldMount.mountPoint.id) === -1)
  for (const mount of deletedMounts) {
    emit('files:node:deleted', mount.mountPoint)
  }
  for (const mount of newMounts) {
    emit('files:node:created', mount.mountPoint)
  }
}

const getJobIdFromOperation = (operation: string, archivePath: string, mountPath: string) => {
  return md5(operation + archivePath + mountPath)
}

const getJobIdFromJob = (job: ArchiveJob) => {
  return getJobIdFromOperation(job.target, job.sourcePath, job.destinationPath)
}

/**
 * @param fileName TBD.
 *
 * @param silent TBD.
 */
async function getPendingJobs(fileName: string, silent?: boolean) {
  if (silent !== true) {
    ++loading.value
  }
  fileName = encodeURIComponent(fileName)
  const url = generateAppUrl('archive/schedule/{operation}/{fileName}', { operation: 'status', fileName })
  try {
    const response = await axios.get<ArchiveJob[]>(url)
    const responseData = response.data
    const jobs: Record<string, ArchiveJob> = {}
    for (const job of responseData) {
      jobs[getJobIdFromJob(job)] = job
    }
    for (const jobId of Object.keys(pendingJobs.value)) {
      if (!jobs[jobId]) {
        delete pendingJobs.value[jobId]
      }
    }
    for (const [jobId, job] of Object.entries(jobs)) {
      pendingJobs.value[jobId] = job
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
  removed?: ArchiveJob[]
  messages?: string[]
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
        delete pendingJobs.value[jobId]
      }
    }
  }
}

const mountPointInfoToMountPoint = (mount: ArchiveMountEntity|ArchiveMountDTO, mountPointInfo?: FileInfoDTO<'folder'>) => {
  const mountPoint = fileInfoToNode(mountPointInfo ?? (mount as ArchiveMountDTO).mountPoint)
  mountPoint.attributes['is-mount-root'] = true
  return {
    ...mount,
    mountPoint,
  } as ArchiveMount
}

const mountPointInfosToNodes = (mounts: ArchiveMount<FileInfoDTO<'folder'>>[]) =>
  mounts.map((mount) => mountPointInfoToMountPoint(mount, mount.mountPoint))

/**
 * @param fileName TBD.
 *
 * @param silent TBD.
 */
async function getArchiveMounts(fileName: string, silent?: boolean) {
  const result = {
    mounts: [] as ArchiveMount[],
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
    result.mounts = mountPointInfosToNodes(responseData.mounts)
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
      result.mounts = mountPointInfosToNodes(responseData.mounts)
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
    const response = await axios.post<ArchiveMountDTO>(url, requestData)
    if (!archiveMountBackgroundJob.value) {
      const newFileId = `${response.data.mountPoint.fileid}`
      if (archiveMounts.value.findIndex((mount) => mount.mountPoint.id === newFileId) === -1) {
        const newMount = mountPointInfoToMountPoint(response.data, response.data.mountPoint)
        newMount.mountPoint.attributes['is-mount-root'] = true
        archiveMounts.value.push(newMount)
        emit('files:node:created', newMount.mountPoint)
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
    emit('files:node:deleted', mount.mountPoint)
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
          if (message.textContent) {
            messages.push(message.textContent)
          }
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
    const response = await axios.post<{ targetFolder: FileInfoDTO<'folder'> }>(url, requestData)
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
  logger.info('PASPHRASE', { archivePassPhrase })
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

const filesAppMountPointUrl = (mountPoint: ArchiveMount) => {
  return generateUrl('/apps/files') + '?dir=' + encodeURIComponent(mountPoint.mountPointPath)
}

const onNotification = (event: NextcloudEvents['notifications:notification:received']) => {
  if (event?.notification?.app !== appName) {
    return
  }
  logger.info('APP NOTIFICATION RECEIVED', { event })
  const destinationData = event?.notification?.messageRichParameters?.destination as DestinationParameter
  switch (destinationData?.status) {
    case 'mount': {
      const mountData = destinationData?.mount
      if (!mountData) {
        logger.error('No mount info in mount notification event')
        return
      }
      let mount: ArchiveMountEntity
      try {
        mount = JSON.parse(mountData)
      } catch (error) {
        logger.error('files_archive, unable to decode mount entity', { event, mountData, error })
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
      const mountIndex = archiveMounts.value.findIndex((mount) => mount.mountPoint.id === mountFileId)
      if (mountIndex === -1) {
        try {
          const newMount = mountPointInfoToMountPoint(mount, JSON.parse(destinationData.folder) as FileInfoDTO<'folder'>)
          archiveMounts.value.push(newMount)
        } catch (error) {
          logger.error('Unable to decode mount point folder file-info record.', { destinationData, error })
        }
      }
      break
    }
    case 'extract':
      logger.info('EXTRACT, SHOULD DO SOMETHING')
      break
  }
}

/**
 * A listener tracking renaming of the moint point and the archive
 * file. Note that using a watch on props.node is not a good idea for
 * monitoring the archive file node as alterations on the node occur
 * before the DAV-backend has performed its file actions.
 *
 * @param node Any node, we determine if it of interest for us.
 */
const onNodeRenamedd = async (node: INode) => {
  // update the list of mountpoints
  const nodeId = node.id

  if (nodeId === props.node.id) {
    // archive file has been renamed, just update all the data.
    await update()
    logger.debug('AFTER ARCHIVE FILE RENAME', { node, propsNode: props.node, equal: node === props.node })
    return
  }

  const mountIndex = archiveMounts.value.findIndex((mount) => mount.mountPoint.id === nodeId)
  if (mountIndex >= 0) {
    logger.info('BERFORE RENAME', { ...archiveMounts[mountIndex] })
    const mount = archiveMounts.value[mountIndex]
    mount.mountPoint = node as IFolder
    mount.mountPointPath = node.path
    mount.mountPointPathHash = md5(node.path)
    logger.debug('AFTER MOUNT POINT RENAME', { ...mount })
    return
  }

  logger.debug('RENAME OF NODE NOT FOR US', { node })
}

/**
 * Monitor deletion of either the archive file or associated mount point(s).
 *
 * @param node Any node, we determine if it is of interest to us.
 */
const onNodeDeleted = (node: INode) => {
  const nodeId = node.id

  if (nodeId === props.node.id) {
    // in this case we have to trigger a reload of the file-list.
    for (const mount of archiveMounts.value) {
      emit('files:node:deleted', mount.mountPoint)
    }
    archiveMounts.value = []

    logger.debug('ARCHIVE FILE HAS BEEN DELETED')
    return
  }

  const mountIndex = archiveMounts.value.findIndex((mount) => mount.mountPoint.id === nodeId)
  if (mountIndex >= 0) {
    archiveMounts.value.splice(mountIndex, 1)
    logger.debug('RECORD UNMOUNT', {
      node,
      archiveMounts: archiveMounts.value,
    })
    return
  }

  logger.debug('DELETE OF NODE NOT FOR US', {
    node,
    archiveMounts: archiveMounts.value,
  })
}

logger.debug('PROPS', { ...props })

// run this once
update()

onBeforeMount(() => {
  subscribe('files:node:deleted', onNodeDeleted)
  subscribe('files:node:renamed', onNodeRenamedd)
  subscribe('notifications:notification:received', onNotification)
})

onUnmounted(() => {
  unsubscribe('files:node:deleted', onNodeDeleted)
  unsubscribe('files:node:renamed', onNodeRenamedd)
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
  .files-tab-entry {
    min-height:44px;
    &.clickable {
      &, & * {
        cursor:pointer;
      }
    }
    .title-annotation::before {
        content: ' ';
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
        margin: 0;
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
    :deep(.list-item__wrapper) {
      .list-item__anchor {
        height: fit-content;
        .list-item-content__subname {
          white-space: normal;
        }
        .list-item-content__name {
          a {
            display: block;
            overflow: inherit;
            text-overflow: inherit;
          }
        }
      }
      .list-item-content__actions {
        align-self: flex-start;
      }
    }
  }
}
</style>
