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
    <div>
      <pre>{{ JSON.stringify(archiveInfo, null, 2) }}</pre>
    </div>
  </div>
</template>
<script>

import { appName } from '../config.js'
import { getInitialState } from '../services/InitialStateService.js'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'

export default {
  name: 'FilesTab',
  components: {
  },
  mixins: [
  ],
  data() {
    return {
      initialState: {},
      fileInfo: {},
      archiveInfo: {},
    };
  },
  created() {
    // this.getData()
  },
  mounted() {
    this.getData()
  },
  computed: {
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
      this.getData();
    },
    /**
     * Fetch some needed data ...
     */
    async getData() {
      this.initialState = getInitialState()
      console.info('INITIAL STATE', this.initialState)

      const fileName = encodeURIComponent(this.fileInfo.path + '/' + this.fileInfo.name);
      const infoUrl = generateUrl('/apps/' + appName + '/archive/info/{fileName}', { fileName });
      try {
        const response = await axios.get(infoUrl);
        console.info('RESPONSE', response);
        this.archiveInfo = response.data;
      } catch (e) {
        console.error('ERROR', e);
        this.archiveInfo = {};
      }
    },
  },
}
</script>
<style lang="scss" scoped>
.files-tab {
}
</style>
