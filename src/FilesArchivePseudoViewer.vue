<!--
 - @copyright Copyright (c) 2025 Claus-Justus Heine <himself@claus-justus-heine.de>
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
  <div />
</template>
<script lang="ts">
// this "viewer" component implements an "mount archive on mouse-click" action.
import logger from './console.ts'
import { appName } from './config.ts'
import mount from './services/mount.ts'

// does not seem to work too well with "setup"

export default {
  name: 'FilesArchivePseudoViewer',
  components: {},
  // an entire FileInfo + extra properties are provided as props
  props: {
    size: {
      type: Number,
      required: true,
    },
    source: {
      type: String,
      required: true,
    },
    permissions: {
      type: String,
      required: true,
    },
    showLoading: {
      type: Boolean,
      default: false,
    },
    fileVersion: {
      type: Number,
      default: null,
    },
    basename: {
      type: String,
      default: null,
    },
    // name with path
    filename: {
      type: String,
      default: null,
    },
  },
  mounted() {
    logger.info('PROPS', { appName, self: this })
    mount(this.filename)
    this.$parent.close()
  },
}
</script>
