(self["webpackChunkfiles_archive"] = self["webpackChunkfiles_archive"] || []).push([["src_files-tab_ts"],{

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts"
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts ***!
  \*********************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm.js");
/* harmony import */ var _config_ts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../config.ts */ "./src/config.ts");
/* harmony import */ var _toolkit_util_initial_state_ts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../toolkit/util/initial-state.ts */ "./src/toolkit/util/initial-state.ts");
/* harmony import */ var _nextcloud_router__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @nextcloud/router */ "./node_modules/@nextcloud/router/dist/index.js");
/* harmony import */ var _nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @nextcloud/event-bus */ "./node_modules/@nextcloud/event-bus/dist/index.mjs");
/* harmony import */ var _nextcloud_auth__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @nextcloud/auth */ "./node_modules/@nextcloud/auth/dist/index.mjs");
/* harmony import */ var _toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../toolkit/util/generate-url.ts */ "./src/toolkit/util/generate-url.ts");
/* harmony import */ var _toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../toolkit/util/file-node-helper.ts */ "./src/toolkit/util/file-node-helper.ts");
/* harmony import */ var js_md5__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! js-md5 */ "./node_modules/js-md5/src/md5.js");
/* harmony import */ var js_md5__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(js_md5__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @nextcloud/dialogs */ "./node_modules/@nextcloud/dialogs/dist/index.mjs");
/* harmony import */ var _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @nextcloud/l10n */ "./node_modules/@nextcloud/l10n/dist/index.mjs");
/* harmony import */ var vue_material_design_icons_NetworkOff_vue__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! vue-material-design-icons/NetworkOff.vue */ "./node_modules/vue-material-design-icons/NetworkOff.vue");
/* harmony import */ var vue_material_design_icons_Cancel_vue__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! vue-material-design-icons/Cancel.vue */ "./node_modules/vue-material-design-icons/Cancel.vue");
/* harmony import */ var _nextcloud_files__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @nextcloud/files */ "./node_modules/@nextcloud/files/dist/index.mjs");
/* harmony import */ var _nextcloud_vue__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @nextcloud/vue */ "./node_modules/@nextcloud/vue/dist/index.mjs");
/* harmony import */ var _rotdrop_nextcloud_vue_components_lib_components_FilePrefixPicker_vue__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! @rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue */ "./node_modules/@rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue");
/* harmony import */ var _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! @nextcloud/axios */ "./node_modules/@nextcloud/axios/dist/index.js");
/* harmony import */ var _toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../toolkit/types/axios-type-guards.ts */ "./src/toolkit/types/axios-type-guards.ts");
/* harmony import */ var _toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ../toolkit/util/nextcloud-sidebar-root.ts */ "./src/toolkit/util/nextcloud-sidebar-root.ts");
/* harmony import */ var _console_ts__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ../console.ts */ "./src/console.ts");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == typeof i ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != typeof t || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != typeof i) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }





















/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (/*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.defineComponent)({
  __name: 'FilesTab',
  setup(__props, _ref) {
    let expose = _ref.expose;
    (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(false); // needs to be done once while in setup mode
    const archivePassPhraseComponent = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(null);
    const mountOptionsComponent = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(null);
    const extractionOptionsComponent = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(null);
    const loading = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(0);
    const fileInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(undefined);
    const fileName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => fileInfo.value ? fileInfo.value.path + '/' + fileInfo.value.name : null);
    const archiveFileId = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      var _fileInfo$value;
      return (_fileInfo$value = fileInfo.value) === null || _fileInfo$value === void 0 ? void 0 : _fileInfo$value.id;
    });
    const ArchiveStatusOk = 0;
    const ArchiveStatusTooLarge = 1;
    const ArchiveStatusBomb = 2;
    const archiveInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(undefined);
    const archiveStatus = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(undefined);
    const archiveMounts = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)([]);
    const pendingJobs = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({});
    const jobsArePending = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => Object.keys(pendingJobs.value).length > 0);
    const initialState = (0,_toolkit_util_initial_state_ts__WEBPACK_IMPORTED_MODULE_2__["default"])();
    const archiveMountStripCommonPathPrefix = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!(initialState !== null && initialState !== void 0 && initialState.mountStripCommonPathPrefixDefault));
    const archiveExtractStripCommonPathPrefix = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!(initialState !== null && initialState !== void 0 && initialState.extractStripCommonPathPrefixDefault));
    const archiveMountBackgroundJob = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!(initialState !== null && initialState !== void 0 && initialState.mountBackgroundJob));
    const archiveExtractBackgroundJob = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!(initialState !== null && initialState !== void 0 && initialState.extractBackgroundJob));
    const archivePassPhrase = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(undefined);
    const showArchiveInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(true);
    const showArchivePassPhrase = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    const showArchiveMounts = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    const showArchiveExtraction = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    const showPendingJobs = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    const openMountTarget = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => (0,js_md5__WEBPACK_IMPORTED_MODULE_8__.md5)((0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_3__.generateUrl)('') + _config_ts__WEBPACK_IMPORTED_MODULE_1__.appName + '-open-archive-mount'));
    const archiveMountFileInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.reactive)({
      dirName: '',
      baseName: ''
    });
    const archiveExtractFileInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.reactive)({
      dirName: '',
      baseName: ''
    });
    const archiveMountBaseName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveMountFileInfo.baseName;
      },
      set(value) {
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(archiveMountFileInfo, 'baseName', value);
        return value;
      }
    });
    const archiveMountDirName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveMountFileInfo.dirName;
      },
      set(value) {
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(archiveMountFileInfo, 'dirName', value);
        return value;
      }
    });
    const archiveExtractBaseName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveExtractFileInfo.baseName;
      },
      set(value) {
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(archiveExtractFileInfo, 'baseName', value);
        return value;
      }
    });
    const archiveExtractDirName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveExtractFileInfo.dirName;
      },
      set(value) {
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(archiveExtractFileInfo, 'dirName', value);
        return value;
      }
    });
    const archiveMountPathName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => archiveMountDirName.value + (archiveMountBaseName.value ? '/' + archiveMountBaseName.value : ''));
    const archiveExtractPathName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => archiveExtractDirName.value + (archiveExtractBaseName.value ? '/' + archiveExtractBaseName.value : ''));
    const archiveStatusText = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      if (archiveStatus.value === ArchiveStatusOk) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'ok');
      } else if (archiveStatus.value & ArchiveStatusBomb) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'zip bomb');
      } else if (archiveStatus.value & ArchiveStatusTooLarge) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'too large');
      }
      return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'unknown');
    });
    // const archiveFileDirName = computed(() => fileInfo.value?.path)
    // const archiveFileBaseName = computed(() => fileInfo.value?.name)
    // const archiveFilePathName = computed(() => fileInfo.value?.path + '/' + fileInfo.value?.name)
    const archiveMounted = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => archiveMounts.value.length > 0);
    // const archiveInfoText = computed(() => JSON.stringify(archiveInfo.value, null, 2))
    const humanArchiveOriginalSize = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      var _archiveInfo$value;
      return !isNaN(parseInt('' + ((_archiveInfo$value = archiveInfo.value) === null || _archiveInfo$value === void 0 ? void 0 : _archiveInfo$value.originalSize))) ? (0,_nextcloud_files__WEBPACK_IMPORTED_MODULE_13__.formatFileSize)(archiveInfo.value.originalSize) : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'unknown');
    });
    const humanArchiveCompressedSize = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      var _archiveInfo$value2;
      return !isNaN(parseInt('' + ((_archiveInfo$value2 = archiveInfo.value) === null || _archiveInfo$value2 === void 0 ? void 0 : _archiveInfo$value2.compressedSize))) ? (0,_nextcloud_files__WEBPACK_IMPORTED_MODULE_13__.formatFileSize)(archiveInfo.value.compressedSize) : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'unknown');
    });
    const humanArchiveFileSize = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      var _archiveInfo$value3;
      return !isNaN(parseInt('' + ((_archiveInfo$value3 = archiveInfo.value) === null || _archiveInfo$value3 === void 0 ? void 0 : _archiveInfo$value3.size))) ? (0,_nextcloud_files__WEBPACK_IMPORTED_MODULE_13__.formatFileSize)(archiveInfo.value.size) : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'unknown');
    });
    const numberOfArchiveMembers = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      var _archiveInfo$value4, _archiveInfo$value5, _archiveInfo$value6;
      if (!archiveInfo.value || ((_archiveInfo$value4 = archiveInfo.value) === null || _archiveInfo$value4 === void 0 ? void 0 : _archiveInfo$value4.numberOfFiles) === undefined || isNaN(parseInt('' + ((_archiveInfo$value5 = archiveInfo.value) === null || _archiveInfo$value5 === void 0 ? void 0 : _archiveInfo$value5.numberOfFiles)))) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'unknown');
      }
      return '' + ((_archiveInfo$value6 = archiveInfo.value) === null || _archiveInfo$value6 === void 0 ? void 0 : _archiveInfo$value6.numberOfFiles);
    });
    const commonPathPrefix = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => !archiveInfo.value || archiveInfo.value.commonPathPrefix === undefined ? (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'unknown') : '/' + archiveInfo.value.commonPathPrefix);
    // const mountPointTitle = computed(() =>
    //   t(appName, 'Mount Points')
    //     + ' ('
    //     + (archiveMounted ? archiveMounts.value.length : t(appName, 'not mounted'))
    //     + ')'
    // )
    // We ____DO____  want to compare numerically here.
    const isLt = (a, b) => a < b;
    const openMountOptionsMenu = () => {
      var _mountOptionsComponen;
      (_mountOptionsComponen = mountOptionsComponent.value) === null || _mountOptionsComponen === void 0 || _mountOptionsComponen.openMenu();
    };
    const openExtractionOptionsMenu = () => {
      var _extractionOptionsCom;
      (_extractionOptionsCom = extractionOptionsComponent.value) === null || _extractionOptionsCom === void 0 || _extractionOptionsCom.openMenu();
    };
    /**
     * Fetch some needed data ...
     */
    const getData = async () => {
      archiveMountStripCommonPathPrefix.value = !!(initialState !== null && initialState !== void 0 && initialState.mountStripCommonPathPrefixDefault);
      archiveExtractStripCommonPathPrefix.value = !!(initialState !== null && initialState !== void 0 && initialState.extractStripCommonPathPrefixDefault);
      archiveMountBackgroundJob.value = !!(initialState !== null && initialState !== void 0 && initialState.mountBackgroundJob);
      archiveExtractBackgroundJob.value = !!(initialState !== null && initialState !== void 0 && initialState.extractBackgroundJob);
      if (!fileName.value) {
        return;
      }
      getArchiveInfo(fileName.value);
      refreshArchiveMounts(fileName.value, true);
      getPendingJobs(fileName.value, true);
    };
    /**
     * Update current fileInfo and fetch new data
     * @param newFileInfo the current file FileInfo
     */
    const update = async newFileInfo => {
      fileInfo.value = newFileInfo;
      /* this.fileList = OCA.Files.App.currentFileList
       * this.fileList.$el.off('updated').on('updated', function(event) {
       *   logger.info('FILE LIST UPDATED, ARGS', arguments)
       * }) */
      archiveMountBaseName.value = fileInfo.value.name.split('.')[0];
      archiveMountDirName.value = fileInfo.value.path;
      archiveExtractBaseName.value = archiveMountBaseName.value;
      archiveExtractDirName.value = archiveMountDirName.value;
      getData();
    };
    expose({
      update
    });
    const getArchiveInfo = async fileName => {
      var _archiveInfo$value7, _archiveInfo$value8;
      ++loading.value;
      fileName = encodeURIComponent(fileName);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_6__["default"])('archive/info/{fileName}', {
        fileName
      });
      const requestData = {};
      if (archivePassPhrase.value) {
        requestData.passPhrase = archivePassPhrase.value;
      }
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].post(url, requestData);
        const responseData = response.data;
        archiveInfo.value = responseData.archiveInfo;
        archiveStatus.value = responseData.archiveStatus;
        if (responseData.messages) {
          for (const message of responseData.messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showInfo)(message);
          }
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e) && e.response.data) {
          const responseData = e.response.data;
          archiveInfo.value = responseData.archiveInfo;
          archiveStatus.value = responseData.archiveStatus;
          if (responseData.messages) {
            for (const message of responseData.messages) {
              (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
                timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
              });
            }
          }
        } else {
          archiveInfo.value = undefined;
        }
      }
      if ((_archiveInfo$value7 = archiveInfo.value) !== null && _archiveInfo$value7 !== void 0 && _archiveInfo$value7.defaultMountPoint) {
        archiveMountBaseName.value = archiveInfo.value.defaultMountPoint;
      }
      if ((_archiveInfo$value8 = archiveInfo.value) !== null && _archiveInfo$value8 !== void 0 && _archiveInfo$value8.defaultTargetBaseName) {
        archiveExtractBaseName.value = archiveInfo.value.defaultTargetBaseName;
      }
      --loading.value;
    };
    const refreshArchiveMounts = async (filename, noEmit) => {
      const oldMounts = [...archiveMounts.value];
      const mounts = await getArchiveMounts(filename, false);
      archiveMounts.value = mounts.mounts;
      if (noEmit) {
        // do no emit birth during initialization
        return;
      }
      // emit birth and death signals as needed, in order to update
      // the frontend file-listing. The computational effort is
      // quadratic, but we are talking here about the common case that
      // there is only a single mount -- or by accident another
      // one. So what.
      const newMounts = archiveMounts.value.filter(mount => oldMounts.findIndex(oldMount => mount.mountPoint.fileid === oldMount.mountPoint.fileid) === -1);
      const deletedMounts = oldMounts.filter(oldMount => archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === oldMount.mountPoint.fileid) === -1);
      for (const mount of deletedMounts) {
        const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_7__.fileInfoToNode)(mount.mountPoint);
        node.attributes['is-mount-root'] = true;
        (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:deleted', node);
      }
      for (const mount of newMounts) {
        const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_7__.fileInfoToNode)(mount.mountPoint);
        node.attributes['is-mount-root'] = true;
        (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:created', node);
      }
    };
    const getJobIdFromOperation = (operation, archivePath, mountPath) => {
      return (0,js_md5__WEBPACK_IMPORTED_MODULE_8__.md5)(operation + archivePath + mountPath);
    };
    const getJobIdFromJob = job => {
      return getJobIdFromOperation(job.target, job.sourcePath, job.destinationPath);
    };
    const getPendingJobs = async (fileName, silent) => {
      if (silent !== true) {
        ++loading.value;
      }
      fileName = encodeURIComponent(fileName);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_6__["default"])('archive/schedule/{operation}/{fileName}', {
        operation: 'status',
        fileName
      });
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].get(url);
        const responseData = response.data;
        const jobs = {};
        for (const job of responseData) {
          jobs[getJobIdFromJob(job)] = job;
        }
        for (const jobId of Object.keys(pendingJobs.value)) {
          if (!jobs[jobId]) {
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.del)(pendingJobs.value, jobId);
          }
        }
        for (const _ref2 of Object.entries(jobs)) {
          var _ref3 = _slicedToArray(_ref2, 2);
          const jobId = _ref3[0];
          const job = _ref3[1];
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(pendingJobs.value, jobId, job);
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e) && e.response.data) {
          const responseData = e.response.data;
          if (responseData.messages) {
            for (const message of responseData.messages) {
              (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
                timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
              });
            }
          }
        }
      }
      if (silent !== true) {
        --loading.value;
      }
    };
    const cancelPendingOperation = async operation => {
      const archivePath = encodeURIComponent(fileName.value);
      const mountPath = encodeURIComponent(archiveMountPathName.value);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_6__["default"])('archive/schedule/{operation}/{archivePath}/{mountPath}', {
        operation,
        archivePath,
        mountPath
      });
      let responseData = {};
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].delete(url, {});
        responseData = response.data;
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            responseData = e.response.data;
            if (Array.isArray(responseData.messages)) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'Cancelling the background job failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      if (responseData.removed) {
        for (const job of responseData.removed) {
          const jobId = getJobIdFromJob(job);
          if (pendingJobs.value[jobId]) {
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.del)(pendingJobs.value, jobId);
          }
        }
      }
    };
    const getArchiveMounts = async (fileName, silent) => {
      const result = {
        mounts: [],
        mounted: false
      };
      if (silent !== true) {
        ++loading.value;
      }
      fileName = encodeURIComponent(fileName);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_6__["default"])('archive/mount/{fileName}', {
        fileName
      });
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].get(url);
        const responseData = response.data;
        result.mounts = responseData.mounts;
        result.mounted = responseData.mounted;
        if (responseData.messages) {
          for (const message of responseData.messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showInfo)(message);
          }
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e) && e.response.data) {
          const responseData = e.response.data;
          result.mounts = responseData.mounts;
          result.mounted = responseData.mounted;
          if (responseData.messages) {
            for (const message of responseData.messages) {
              (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
                timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
              });
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
          archivePassPhrase.value = mount.archivePassPhrase;
        }
        delete mount.archivePassPhrase;
      }
      if (silent !== true) {
        --loading.value;
      }
      return result;
    };
    const mountArchive = async () => {
      const archivePath = encodeURIComponent(fileName.value);
      const mountPath = encodeURIComponent(archiveMountPathName.value);
      const urlTemplate = archiveMountBackgroundJob.value ? 'archive/schedule/mount/{archivePath}/{mountPath}' : 'archive/mount/{archivePath}/{mountPath}';
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_6__["default"])(urlTemplate, {
        archivePath,
        mountPath
      });
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(true);
      const requestData = {};
      if (archivePassPhrase.value) {
        requestData.passPhrase = archivePassPhrase.value;
      }
      requestData.stripCommonPathPrefix = !!archiveMountStripCommonPathPrefix.value;
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].post(url, requestData);
        if (!archiveMountBackgroundJob.value) {
          const newMount = response.data;
          const newFileId = newMount.mountPoint.fileid;
          if (archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === newFileId) === -1) {
            archiveMounts.value.push(newMount);
            const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_7__.fileInfoToNode)(response.data.mountPoint);
            node.attributes['is-mount-root'] = true;
            (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:created', node);
          }
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'Mount request failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      if (archiveMountBackgroundJob.value) {
        getPendingJobs(fileName.value, true);
      }
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(false);
    };
    const unmount = async mount => {
      const cloudUser = (0,_nextcloud_auth__WEBPACK_IMPORTED_MODULE_5__.getCurrentUser)();
      const url = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_3__.generateRemoteUrl)('dav/files/' + cloudUser.uid + mount.mountPointPath);
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(true);
      try {
        await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].delete(url);
        const mountIndex = archiveMounts.value.indexOf(mount);
        if (mountIndex >= 0) {
          archiveMounts.value.splice(mountIndex, 1);
        } else {
          _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('UNABLE TO FIND DELETED MOUNT IN LIST', mount, archiveMounts);
        }
        const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_7__.fileInfoToNode)(mount.mountPoint);
        node.attributes['is-mount-root'] = true;
        (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:deleted', node);
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        const messages = [];
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e)) {
          var _e$response$request;
          // attempt parsing Sabre exception is available
          const xml = (_e$response$request = e.response.request) === null || _e$response$request === void 0 ? void 0 : _e$response$request.responseXML;
          if (xml && xml.documentElement.localName === 'error' && xml.documentElement.namespaceURI === 'DAV:') {
            const xmlMessages = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'message');
            // const exceptions = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'exception');
            for (const message of xmlMessages) {
              message.textContent && messages.push(message.textContent);
            }
          }
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'Unmount request failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
            });
          }
          if (e.response.status === 404) {
            refreshArchiveMounts(fileName.value, true);
          }
        }
      }
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(false);
    };
    const extractArchive = async () => {
      const archivePath = encodeURIComponent(fileName.value);
      const targetPath = encodeURIComponent(archiveExtractPathName.value);
      const urlTemplate = archiveExtractBackgroundJob.value ? 'archive/schedule/extract/{archivePath}/{targetPath}' : 'archive/extract/{archivePath}/{targetPath}';
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_6__["default"])(urlTemplate, {
        archivePath,
        targetPath
      });
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(true);
      const requestData = {};
      if (archivePassPhrase.value) {
        requestData.passPhrase = archivePassPhrase.value;
      }
      requestData.stripCommonPathPrefix = !!archiveExtractStripCommonPathPrefix.value;
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].post(url, requestData);
        if (!archiveExtractBackgroundJob.value) {
          const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_7__.fileInfoToNode)(response.data.targetFolder);
          node.attributes['is-mount-root'] = true;
          (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:created', node);
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'Archive extraction failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      if (archiveExtractBackgroundJob.value) {
        getPendingJobs(fileName.value, true);
      }
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(false);
    };
    const setPassPhrase = async () => {
      var _archivePassPhraseCom, _archivePassPhraseCom2;
      const newPassPhrase = showArchivePassPhrase.value ? ((_archivePassPhraseCom = archivePassPhraseComponent.value.$el.querySelector('input[type="text"]')) === null || _archivePassPhraseCom === void 0 ? void 0 : _archivePassPhraseCom.value) || '' : ((_archivePassPhraseCom2 = archivePassPhraseComponent.value.$el.querySelector('input[type="password"]')) === null || _archivePassPhraseCom2 === void 0 ? void 0 : _archivePassPhraseCom2.value) || '';
      archivePassPhrase.value = newPassPhrase;
      // patch it into existing mounts if any
      const archivePath = encodeURIComponent(fileName.value);
      const url = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_3__.generateUrl)('/apps/' + _config_ts__WEBPACK_IMPORTED_MODULE_1__.appName + '/archive/mount/{archivePath}', {
        archivePath
      });
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(true);
      const requestData = {
        changeSet: {
          archivePassPhrase: archivePassPhrase.value
        }
      };
      try {
        await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_16__["default"].patch(url, requestData);
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_17__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_1__.appName, 'Patching the passphrase failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_9__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      (0,_toolkit_util_nextcloud_sidebar_root_ts__WEBPACK_IMPORTED_MODULE_18__.setSidebarNodeBusy)(false);
    };
    const togglePassPhraseVisibility = async () => {
      // this is sooo complicated because the NC NcAction controls are
      // seemingly only pro-forma vue-controls. There is no working
      // v-model support, e.g.
      let visibleElement = showArchivePassPhrase.value ? archivePassPhraseComponent.value.$el.querySelector('input[type="text"]') : archivePassPhraseComponent.value.$el.querySelector('input[type="password"]');
      const currentValue = visibleElement.value;
      showArchivePassPhrase.value = !showArchivePassPhrase.value;
      await (0,vue__WEBPACK_IMPORTED_MODULE_0__.nextTick)();
      visibleElement = showArchivePassPhrase.value ? archivePassPhraseComponent.value.$el.querySelector('input[type="text"]') : archivePassPhraseComponent.value.$el.querySelector('input[type="password"]');
      visibleElement.value = currentValue;
    };
    const filesAppMountPointUrl = mountPoint => {
      return (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_3__.generateUrl)('/apps/files') + '?dir=' + encodeURIComponent(mountPoint.mountPointPath);
    };
    const onNotification = event => {
      var _event$notification, _event$notification2;
      if ((event === null || event === void 0 || (_event$notification = event.notification) === null || _event$notification === void 0 ? void 0 : _event$notification.app) !== _config_ts__WEBPACK_IMPORTED_MODULE_1__.appName) {
        return;
      }
      const destinationData = event === null || event === void 0 || (_event$notification2 = event.notification) === null || _event$notification2 === void 0 || (_event$notification2 = _event$notification2.messageRichParameters) === null || _event$notification2 === void 0 ? void 0 : _event$notification2.destination;
      if ((destinationData === null || destinationData === void 0 ? void 0 : destinationData.status) !== 'mount') {
        return; // nothing special ATM
      }
      const mountData = destinationData === null || destinationData === void 0 ? void 0 : destinationData.mount;
      if (!mountData) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('No mount info in mount notification event');
        return;
      }
      let mount;
      try {
        mount = JSON.parse(mountData);
      } catch (error) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('files_archive, unable to decode mount entity', {
          event,
          mountData
        });
        return;
      }
      if (mount.archiveFileId !== archiveFileId.value) {
        // not for us, in the future we may want to maintain a store
        // and cache the data for all file-ids.
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].info('*** Archive notification for other file received', event);
        return;
      }
      const jobId = getJobIdFromOperation('mount', mount.archiveFilePath, mount.mountPointPath);
      if (pendingJobs.value[jobId]) {
        delete pendingJobs.value[jobId];
      }
      _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].info('*** Mount notification received, updating mount-list', destinationData);
      const mountFileId = destinationData.id;
      const mountIndex = archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === mountFileId);
      if (mountIndex === -1) {
        try {
          archiveMounts.value.push(_objectSpread(_objectSpread({}, mount), {}, {
            mountPoint: JSON.parse(destinationData.folder)
          }));
        } catch (error) {
          _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].error('Unable to decode mount point folder file-info record.', {
            destinationData
          });
        }
      }
    };
    const onMountPointRenamed = mountPoint => {
      // update the list of mountpoints
      const mountFileId = mountPoint.fileid;
      const mountIndex = archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === mountFileId);
      if (mountIndex >= 0) {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].info('BERFORE RENAME', _objectSpread({}, archiveMounts[mountIndex]));
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(archiveMounts[mountIndex], 'mountPoint', mountPoint);
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(archiveMounts[mountIndex], 'mountPointPath', mountPoint.path);
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.set)(archiveMounts[mountIndex], 'mountPointPathHash', (0,js_md5__WEBPACK_IMPORTED_MODULE_8__.md5)(mountPoint.path));
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].info('AFTER RENAME', _objectSpread({}, archiveMounts[mountIndex]));
      } else {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].info('RENAME OF NODE NOT FOR US', mountPoint);
      }
    };
    const onMountPointDeleted = mountPoint => {
      const mountFileId = mountPoint.fileid;
      const mountIndex = archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === mountFileId);
      if (mountIndex >= 0) {
        archiveMounts.value.splice(mountIndex, 1);
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].info('RECORD UNMOUNT', mountPoint);
      } else {
        _console_ts__WEBPACK_IMPORTED_MODULE_19__["default"].info('DELETE OF NODE NOT FOR US', {
          mountPoint,
          archiveMounts: archiveMounts.value
        });
      }
    };
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.onBeforeMount)(() => {
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.subscribe)('files:node:deleted', onMountPointDeleted);
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.subscribe)('files:node:renamed', onMountPointRenamed);
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.subscribe)('notifications:notification:received', onNotification);
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.onUnmounted)(() => {
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.unsubscribe)('files:node:deleted', onMountPointDeleted);
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.unsubscribe)('files:node:renamed', onMountPointRenamed);
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.unsubscribe)('notifications:notification:received', onNotification);
    });
    return {
      __sfc: true,
      archivePassPhraseComponent,
      mountOptionsComponent,
      extractionOptionsComponent,
      loading,
      fileInfo,
      fileName,
      archiveFileId,
      ArchiveStatusOk,
      ArchiveStatusTooLarge,
      ArchiveStatusBomb,
      archiveInfo,
      archiveStatus,
      archiveMounts,
      pendingJobs,
      jobsArePending,
      initialState,
      archiveMountStripCommonPathPrefix,
      archiveExtractStripCommonPathPrefix,
      archiveMountBackgroundJob,
      archiveExtractBackgroundJob,
      archivePassPhrase,
      showArchiveInfo,
      showArchivePassPhrase,
      showArchiveMounts,
      showArchiveExtraction,
      showPendingJobs,
      openMountTarget,
      archiveMountFileInfo,
      archiveExtractFileInfo,
      archiveMountBaseName,
      archiveMountDirName,
      archiveExtractBaseName,
      archiveExtractDirName,
      archiveMountPathName,
      archiveExtractPathName,
      archiveStatusText,
      archiveMounted,
      humanArchiveOriginalSize,
      humanArchiveCompressedSize,
      humanArchiveFileSize,
      numberOfArchiveMembers,
      commonPathPrefix,
      isLt,
      openMountOptionsMenu,
      openExtractionOptionsMenu,
      getData,
      update,
      getArchiveInfo,
      refreshArchiveMounts,
      getJobIdFromOperation,
      getJobIdFromJob,
      getPendingJobs,
      cancelPendingOperation,
      getArchiveMounts,
      mountArchive,
      unmount,
      extractArchive,
      setPassPhrase,
      togglePassPhraseVisibility,
      filesAppMountPointUrl,
      onNotification,
      onMountPointRenamed,
      onMountPointDeleted,
      appName: _config_ts__WEBPACK_IMPORTED_MODULE_1__.appName,
      t: _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_10__.translate,
      NetworkOffIcon: vue_material_design_icons_NetworkOff_vue__WEBPACK_IMPORTED_MODULE_11__["default"],
      CancelIcon: vue_material_design_icons_Cancel_vue__WEBPACK_IMPORTED_MODULE_12__["default"],
      NcActionInput: _nextcloud_vue__WEBPACK_IMPORTED_MODULE_14__.NcActionInput,
      NcActionCheckbox: _nextcloud_vue__WEBPACK_IMPORTED_MODULE_14__.NcActionCheckbox,
      NcActions: _nextcloud_vue__WEBPACK_IMPORTED_MODULE_14__.NcActions,
      NcActionButton: _nextcloud_vue__WEBPACK_IMPORTED_MODULE_14__.NcActionButton,
      NcListItem: _nextcloud_vue__WEBPACK_IMPORTED_MODULE_14__.NcListItem,
      FilePrefixPicker: _rotdrop_nextcloud_vue_components_lib_components_FilePrefixPicker_vue__WEBPACK_IMPORTED_MODULE_15__["default"]
    };
  }
}));

/***/ },

/***/ "./src/files-tab.ts"
/*!**************************!*\
  !*** ./src/files-tab.ts ***!
  \**************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _config_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./config.ts */ "./src/config.ts");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm.js");
/* harmony import */ var _nextcloud_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @nextcloud/vue */ "./node_modules/@nextcloud/vue/dist/index.mjs");
/* harmony import */ var _views_FilesTab_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./views/FilesTab.vue */ "./src/views/FilesTab.vue");
/* harmony import */ var _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @nextcloud/l10n */ "./node_modules/@nextcloud/l10n/dist/index.mjs");
/**
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @copyright 2025 Claus-Justus Heine
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





vue__WEBPACK_IMPORTED_MODULE_1__["default"].mixin({
  data() {
    return {
      appName: _config_ts__WEBPACK_IMPORTED_MODULE_0__.appName
    };
  },
  methods: {
    t: _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_4__.translate,
    n: _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_4__.translatePlural
  }
});
vue__WEBPACK_IMPORTED_MODULE_1__["default"].directive('tooltip', _nextcloud_vue__WEBPACK_IMPORTED_MODULE_2__.Tooltip);
const FilesTabVue = vue__WEBPACK_IMPORTED_MODULE_1__["default"].extend(_views_FilesTab_vue__WEBPACK_IMPORTED_MODULE_3__["default"]);
const createTabInstance = parent => new FilesTabVue({
  parent
});
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createTabInstance);

/***/ },

/***/ "./src/toolkit/types/axios-type-guards.ts"
/*!************************************************!*\
  !*** ./src/toolkit/types/axios-type-guards.ts ***!
  \************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isAxiosError: () => (/* reexport safe */ axios__WEBPACK_IMPORTED_MODULE_0__.isAxiosError),
/* harmony export */   isAxiosErrorResponse: () => (/* binding */ isAxiosErrorResponse),
/* harmony export */   isAxiosMessagesErrorResponse: () => (/* binding */ isAxiosMessagesErrorResponse)
/* harmony export */ });
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/**
 * Orchestra member, musicion and project management application.
 *
 * CAFEVDB -- Camerata Academica Freiburg e.V. DataBase.
 *
 * @author Claus-Justus Heine
 * @copyright 2025, 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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


// eslint-disable-next-line @typescript-eslint/no-explicit-any
const isAxiosErrorResponse = error => (0,axios__WEBPACK_IMPORTED_MODULE_0__.isAxiosError)(error) && !!error.response;
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const isAxiosMessagesErrorResponse = error => isAxiosErrorResponse(error) && 'messages' in error.response.data;

/***/ },

/***/ "./src/toolkit/util/file-node-helper.ts"
/*!**********************************************!*\
  !*** ./src/toolkit/util/file-node-helper.ts ***!
  \**********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fileInfoToNode: () => (/* binding */ fileInfoToNode)
/* harmony export */ });
/* harmony import */ var _nextcloud_auth__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @nextcloud/auth */ "./node_modules/@nextcloud/auth/dist/index.mjs");
/* harmony import */ var path__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! path */ "../../node_modules/path/path.js");
/* harmony import */ var path__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(path__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _nextcloud_files__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @nextcloud/files */ "./node_modules/@nextcloud/files/dist/index.mjs");
/* harmony import */ var _nextcloud_router__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @nextcloud/router */ "./node_modules/@nextcloud/router/dist/index.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == typeof i ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != typeof t || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != typeof i) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * @copyright Copyright (c) 2024-2026 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
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
 *
 */




/**
 * @param fileInfo File-info object.
 *
 * @param owner If undefined the current user is used.
 *
 * @return Result.
 */
const fileInfoToNode = (fileInfo, owner) => {
  owner = owner || (0,_nextcloud_auth__WEBPACK_IMPORTED_MODULE_0__.getCurrentUser)().uid;
  const userFrontEndFolder = '/' + owner + '/files';
  if (fileInfo.topLevelFolder !== userFrontEndFolder) {
    throw new Error("".concat(fileInfo.path, " is located outside of the front end user file space ").concat(userFrontEndFolder, "."));
  }
  const nodeData = {
    id: parseInt(fileInfo.fileid, 10),
    source: (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_3__.generateRemoteUrl)((0,path__WEBPACK_IMPORTED_MODULE_1__.join)('dav/files', owner, fileInfo.relativePath)),
    root: "/files/".concat(owner),
    mime: fileInfo.mime,
    mtime: new Date(fileInfo.lastmod * 1000),
    owner,
    size: fileInfo.size,
    permissions: fileInfo.permissions,
    attributes: _objectSpread(_objectSpread({}, fileInfo), {}, {
      'has-preview': fileInfo.hasPreview
    })
  };
  return fileInfo.type === 'file' ? new _nextcloud_files__WEBPACK_IMPORTED_MODULE_2__.File(nodeData) : new _nextcloud_files__WEBPACK_IMPORTED_MODULE_2__.Folder(nodeData);
};

/***/ },

/***/ "./src/toolkit/util/generate-url.ts"
/*!******************************************!*\
  !*** ./src/toolkit/util/generate-url.ts ***!
  \******************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   generateOcsUrl: () => (/* binding */ generateOcsUrl),
/* harmony export */   generateUrl: () => (/* binding */ generateUrl)
/* harmony export */ });
/* harmony import */ var _config_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../config.ts */ "./src/config.ts");
/* harmony import */ var _nextcloud_router__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @nextcloud/router */ "./node_modules/@nextcloud/router/dist/index.js");
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == typeof i ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != typeof t || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != typeof i) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * @copyright Copyright (c) 2022-2025 Claus-Justus Heine <himself@claus-justus-heine.de>
 *
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 *
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
 *
 */


/**
 * Generate an absolute URL for this app.
 *
 * @param url The locate URL without app-prefix.
 *
 * @param urlParams Object holding url-parameters if url
 * contains parameters. "Excess" parameters will be appended as query
 * parameters to the URL.
 *
 * @param urlOptions Object with query parameters
 */
const generateUrl = (url, urlParams, urlOptions) => {
  // const str = '/image/{joinTable}/{ownerId}';
  url = url.replace(/^\/+/g, '');
  let generated = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_1__.generateUrl)('/apps/' + _config_ts__WEBPACK_IMPORTED_MODULE_0__.appName + '/' + url, urlParams, urlOptions);
  // remove missing parameters as optional
  generated = generated.replace(/\/%7B[^%]+%7D/g, '');
  const queryParams = _objectSpread({}, urlParams || {});
  for (const urlParam of url.matchAll(/{([^{}]*)}/g)) {
    delete queryParams[urlParam[1]];
  }
  const queryArray = [];
  for (const _ref of Object.entries(queryParams)) {
    var _ref2 = _slicedToArray(_ref, 2);
    const key = _ref2[0];
    const value = _ref2[1];
    try {
      queryArray.push(key + '=' + encodeURIComponent((value === null || value === void 0 ? void 0 : value.toString()) || ''));
    } catch (e) {
      console.debug('STRING CONVERSION ERROR', e);
    }
  }
  if (queryArray.length > 0) {
    generated += '?' + queryArray.join('&');
  }
  return generated;
};
const generateOcsUrl = (url, urlParams, urlOptions) => {
  url = url.replace(/^\/+/g, '');
  let generated = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_1__.generateOcsUrl)('/apps/' + _config_ts__WEBPACK_IMPORTED_MODULE_0__.appName + '/' + url, urlParams, urlOptions);
  // depending on the version of @nextcloud/router there are further duplicate slashes, oh well.
  generated = generated.replace(/\/\/ocs/g, '/ocs');
  const queryParams = _objectSpread({}, urlParams);
  for (const urlParam of url.matchAll(/{([^{}]*)}/g)) {
    delete queryParams[urlParam[1]];
  }
  const queryArray = [];
  for (const _ref3 of Object.entries(queryParams)) {
    var _ref4 = _slicedToArray(_ref3, 2);
    const key = _ref4[0];
    const value = _ref4[1];
    try {
      queryArray.push(key + '=' + encodeURIComponent((value === null || value === void 0 ? void 0 : value.toString()) || ''));
    } catch (e) {
      console.debug('STRING CONVERSION ERROR', e);
    }
  }
  if (queryArray.length > 0) {
    generated += '?' + queryArray.join('&');
  }
  return generated;
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (generateUrl);

/***/ },

/***/ "./src/toolkit/util/nextcloud-sidebar-root.ts"
/*!****************************************************!*\
  !*** ./src/toolkit/util/nextcloud-sidebar-root.ts ***!
  \****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearSidebarNodeBusy: () => (/* binding */ clearSidebarNodeBusy),
/* harmony export */   getSidebarNode: () => (/* binding */ getSidebarNode),
/* harmony export */   getSidebarRoot: () => (/* binding */ getSidebarRoot),
/* harmony export */   setSidebarNodeBusy: () => (/* binding */ setSidebarNodeBusy)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm.js");
/* harmony import */ var _nextcloud_files__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @nextcloud/files */ "./node_modules/@nextcloud/files/dist/index.mjs");
/* harmony import */ var _nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @nextcloud/event-bus */ "./node_modules/@nextcloud/event-bus/dist/index.mjs");
/**
 * @author Claus-Justus Heine
 * @copyright 2025 Claus-Justus Heine <himself@claus-justus-heine.de>
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



let currentInstance = null;
let sidebarRoot = null;
const busyNodes = [];
/**
 * Find the root of the right sidebar in order to get access to
 * interesting properties like the current Node-object.
 */
const getSidebarRoot = () => {
  var _getCurrentInstance;
  let instance = ((_getCurrentInstance = (0,vue__WEBPACK_IMPORTED_MODULE_0__.getCurrentInstance)()) === null || _getCurrentInstance === void 0 ? void 0 : _getCurrentInstance.proxy) || null;
  if (sidebarRoot && (instance && currentInstance === instance || !instance && currentInstance)) {
    return sidebarRoot;
  }
  if (instance) {
    currentInstance = instance;
  } else {
    instance = currentInstance;
  }
  while (instance && instance.$parent && instance.$options.name !== 'SidebarRoot') {
    var _instance;
    instance = (_instance = instance) === null || _instance === void 0 ? void 0 : _instance.$parent;
  }
  sidebarRoot = instance || null;
  return sidebarRoot;
};
const getSidebarNode = () => {
  const sidebarRoot = getSidebarRoot();
  return (sidebarRoot === null || sidebarRoot === void 0 ? void 0 : sidebarRoot.node) || null;
};
const setSidebarNodeBusy = function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  const node = getSidebarNode();
  if (node && state) {
    node.status = _nextcloud_files__WEBPACK_IMPORTED_MODULE_1__.NodeStatus.LOADING;
    (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_2__.emit)('files:node:updated', node);
    busyNodes.push(node);
  } else {
    for (const node of busyNodes) {
      node.status = undefined;
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_2__.emit)('files:node:updated', node);
    }
    busyNodes.splice(0, busyNodes.length);
  }
};
const clearSidebarNodeBusy = () => setSidebarNodeBusy(false);

/***/ },

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true"
/*!***********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true ***!
  \***********************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render),
/* harmony export */   staticRenderFns: () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function render() {
  var _setup$archiveInfo, _setup$archiveInfo2, _setup$archiveInfo3, _setup$archiveInfo4;
  var _vm = this,
    _c = _vm._self._c,
    _setup = _vm._self._setupProxy;
  return _c("div", {
    staticClass: "files-tab"
  }, [_c("ul", [_c("li", {
    staticClass: "files-tab-entry flex flex-center clickable",
    on: {
      click: function ($event) {
        _setup.showArchiveInfo = !_setup.showArchiveInfo;
      }
    }
  }, [_c("div", {
    staticClass: "files-tab-entry__avatar icon-info-white"
  }), _vm._v(" "), _c("div", {
    staticClass: "files-tab-entry__desc"
  }, [_c("h5", [_vm._v(_vm._s(_setup.t(_setup.appName, "Archive Information")))])]), _vm._v(" "), _c(_setup.NcActions, [_c(_setup.NcActionButton, {
    attrs: {
      icon: "icon-triangle-" + (_setup.showArchiveInfo ? "n" : "s")
    },
    model: {
      value: _setup.showArchiveInfo,
      callback: function ($$v) {
        _setup.showArchiveInfo = $$v;
      },
      expression: "showArchiveInfo"
    }
  })], 1)], 1), _vm._v(" "), _c("li", {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: _setup.showArchiveInfo,
      expression: "showArchiveInfo"
    }],
    staticClass: "files-tab-entry"
  }, [_setup.loading ? _c("div", {
    staticClass: "icon-loading-small"
  }) : _vm._e(), _vm._v(" "), _c("ul", {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: !_setup.loading,
      expression: "!loading"
    }],
    staticClass: "archive-info"
  }, [_setup.isLt(0, _setup.archiveStatus) ? _c(_setup.NcListItem, {
    class: {
      "archive-error": _setup.isLt(0, _setup.archiveStatus)
    },
    attrs: {
      name: _setup.t(_setup.appName, "archive status"),
      bold: true,
      details: _setup.archiveStatusText
    },
    scopedSlots: _vm._u([{
      key: "icon",
      fn: function () {
        return [_c("div", {
          staticClass: "icon-error"
        })];
      },
      proxy: true
    }], null, false, 2833062560)
  }) : _vm._e(), _vm._v(" "), _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "archive format"),
      bold: true,
      details: ((_setup$archiveInfo = _setup.archiveInfo) === null || _setup$archiveInfo === void 0 ? void 0 : _setup$archiveInfo.format) || _setup.t(_setup.appName, "unknown"),
      compact: ""
    }
  }), _vm._v(" "), _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "MIME type"),
      bold: true,
      details: ((_setup$archiveInfo2 = _setup.archiveInfo) === null || _setup$archiveInfo2 === void 0 ? void 0 : _setup$archiveInfo2.mimeType) || _setup.t(_setup.appName, "unknown"),
      compact: ""
    }
  }), _vm._v(" "), _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "backend driver"),
      bold: true,
      details: ((_setup$archiveInfo3 = _setup.archiveInfo) === null || _setup$archiveInfo3 === void 0 ? void 0 : _setup$archiveInfo3.backendDriver) || _setup.t(_setup.appName, "unknown"),
      compact: ""
    }
  }), _vm._v(" "), _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "uncompressed size"),
      bold: true,
      details: _setup.humanArchiveOriginalSize,
      compact: ""
    }
  }), _vm._v(" "), _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "compressed size"),
      bold: true,
      details: _setup.humanArchiveCompressedSize,
      compact: ""
    }
  }), _vm._v(" "), _setup.humanArchiveCompressedSize !== _setup.humanArchiveFileSize ? _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "archive file size"),
      bold: true,
      details: _setup.humanArchiveFileSize,
      compact: ""
    }
  }) : _vm._e(), _vm._v(" "), _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "# archive members"),
      bold: true,
      details: _setup.numberOfArchiveMembers,
      compact: ""
    }
  }), _vm._v(" "), _c(_setup.NcListItem, {
    attrs: {
      name: _setup.t(_setup.appName, "common prefix"),
      bold: true,
      details: _setup.commonPathPrefix,
      compact: ""
    }
  }), _vm._v(" "), (_setup$archiveInfo4 = _setup.archiveInfo) !== null && _setup$archiveInfo4 !== void 0 && _setup$archiveInfo4.comment ? _c(_setup.NcListItem, {
    staticClass: "archive-comment",
    attrs: {
      name: _setup.t(_setup.appName, "creator's comment"),
      bold: true,
      compact: ""
    },
    scopedSlots: _vm._u([{
      key: "subtitle",
      fn: function () {
        var _setup$archiveInfo5;
        return [_vm._v("\n            " + _vm._s((_setup$archiveInfo5 = _setup.archiveInfo) === null || _setup$archiveInfo5 === void 0 ? void 0 : _setup$archiveInfo5.comment) + "\n          ")];
      },
      proxy: true
    }], null, false, 3976111642)
  }) : _vm._e()], 1)]), _vm._v(" "), _c("li", {
    staticClass: "files-tab-entry flex flex-center"
  }, [_c("div", {
    staticClass: "files-tab-entry__avatar icon-password-white"
  }), _vm._v(" "), _c("div", {
    staticClass: "files-tab-entry__desc"
  }, [_c("h5", [_c("span", {
    staticClass: "main-title"
  }, [_vm._v(_vm._s(_setup.t(_setup.appName, "Passphrase")))]), _vm._v(" "), !_setup.archivePassPhrase ? _c("span", {
    staticClass: "title-annotation"
  }, [_vm._v("(" + _vm._s(_setup.t(_setup.appName, "unset")) + ")")]) : _vm._e()])]), _vm._v(" "), _c(_setup.NcActions, {
    attrs: {
      "force-menu": true
    }
  }, [_setup.showArchivePassPhrase ? _c(_setup.NcActionInput, {
    ref: "archivePassPhraseComponent",
    attrs: {
      value: _setup.archivePassPhrase,
      type: "text",
      icon: "icon-password"
    },
    on: {
      submit: _setup.setPassPhrase
    }
  }, [_vm._v("\n          " + _vm._s(_setup.t(_setup.appName, "archive passphrase")) + "\n        ")]) : _c(_setup.NcActionInput, {
    ref: "archivePassPhraseComponent",
    attrs: {
      value: _setup.archivePassPhrase,
      type: "password",
      icon: "icon-password"
    },
    on: {
      submit: _setup.setPassPhrase
    }
  }, [_vm._v("\n          " + _vm._s(_setup.t(_setup.appName, "archive passphrase")) + "\n        ")]), _vm._v(" "), _c(_setup.NcActionCheckbox, {
    on: {
      change: _setup.togglePassPhraseVisibility
    }
  }, [_vm._v("\n          " + _vm._s(_setup.t(_setup.appName, "Show Passphrase")) + "\n        ")])], 1)], 1), _vm._v(" "), _c("li", {
    staticClass: "files-tab-entry flex flex-center clickable",
    on: {
      click: function ($event) {
        _setup.showArchiveMounts = !_setup.showArchiveMounts;
      }
    }
  }, [_c("div", {
    staticClass: "files-tab-entry__avatar icon-external-white"
  }), _vm._v(" "), _c("div", {
    staticClass: "files-tab-entry__desc"
  }, [_c("h5", [_c("span", {
    staticClass: "main-title"
  }, [_vm._v(_vm._s(_setup.t(_setup.appName, "Mount Points")))]), _vm._v(" "), _setup.archiveMounted ? _c("span", {
    staticClass: "title-annotation"
  }, [_vm._v("(" + _vm._s("" + _setup.archiveMounts.length) + ")")]) : _c("span", {
    staticClass: "title-annotation"
  }, [_vm._v("(" + _vm._s(_setup.t(_setup.appName, "not mounted")) + ")")])])]), _vm._v(" "), _c(_setup.NcActions, [_c(_setup.NcActionButton, {
    attrs: {
      icon: "icon-triangle-" + (_setup.showArchiveMounts ? "n" : "s")
    },
    model: {
      value: _setup.showArchiveMounts,
      callback: function ($$v) {
        _setup.showArchiveMounts = $$v;
      },
      expression: "showArchiveMounts"
    }
  })], 1)], 1), _vm._v(" "), _c("li", {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: _setup.showArchiveMounts,
      expression: "showArchiveMounts"
    }],
    staticClass: "directory-chooser files-tab-entry"
  }, [_setup.loading ? _c("div", {
    staticClass: "icon-loading-small"
  }) : _setup.archiveMounted ? _c("ul", {
    staticClass: "archive-mounts"
  }, _vm._l(_setup.archiveMounts, function (mountPoint) {
    return _c(_setup.NcListItem, {
      key: mountPoint.id,
      attrs: {
        "force-display-actions": true,
        bold: false
      },
      scopedSlots: _vm._u([{
        key: "title",
        fn: function () {
          return [_c("a", {
            directives: [{
              name: "tooltip",
              rawName: "v-tooltip",
              value: mountPoint.mountPointPath,
              expression: "mountPoint.mountPointPath"
            }],
            staticClass: "external icon-folder icon",
            attrs: {
              target: _setup.openMountTarget,
              href: _setup.filesAppMountPointUrl(mountPoint)
            }
          }, [_vm._v("\n              " + _vm._s(mountPoint.mountPointPath) + "\n            ")])];
        },
        proxy: true
      }, {
        key: "actions",
        fn: function () {
          return [_c(_setup.NcActionButton, {
            on: {
              click: function ($event) {
                return _setup.unmount(mountPoint);
              }
            },
            scopedSlots: _vm._u([{
              key: "icon",
              fn: function () {
                return [_c(_setup.NetworkOffIcon, {
                  directives: [{
                    name: "tooltip",
                    rawName: "v-tooltip",
                    value: _setup.t(_setup.appName, "Disconnect storage"),
                    expression: "t(appName, 'Disconnect storage')"
                  }],
                  attrs: {
                    size: 20
                  }
                })];
              },
              proxy: true
            }], null, true)
          })];
        },
        proxy: true
      }, mountPoint.mountFlags & 1 ? {
        key: "extra",
        fn: function () {
          return [_c("div", [_vm._v(_vm._s(_setup.t(_setup.appName, "Common prefix {prefix} is stripped.", {
            prefix: _setup.commonPathPrefix
          })))])];
        },
        proxy: true
      } : null], null, true)
    });
  }), 1) : _c("div", [_c(_setup.FilePrefixPicker, {
    attrs: {
      hint: _setup.t(_setup.appName, "Not mounted, create a new mount point:"),
      placeholder: _setup.t(_setup.appName, "base name")
    },
    on: {
      submit: _setup.mountArchive
    },
    model: {
      value: _setup.archiveMountFileInfo,
      callback: function ($$v) {
        _setup.archiveMountFileInfo = $$v;
      },
      expression: "archiveMountFileInfo"
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "flex flex-center"
  }, [_c("div", {
    staticClass: "label",
    on: {
      click: _setup.openMountOptionsMenu
    }
  }, [_vm._v("\n            " + _vm._s(_setup.t(_setup.appName, "Mount Options")) + "\n          ")]), _vm._v(" "), _c(_setup.NcActions, {
    ref: "mountOptionsComponent",
    attrs: {
      "force-menu": true
    }
  }, [_c(_setup.NcActionCheckbox, {
    ref: "mountStripCommonPathPrefixComponent",
    attrs: {
      checked: _setup.archiveMountStripCommonPathPrefix
    },
    on: {
      change: function ($event) {
        _setup.archiveMountStripCommonPathPrefix = !_setup.archiveMountStripCommonPathPrefix;
      }
    }
  }, [_vm._v("\n              " + _vm._s(_setup.t(_setup.appName, "strip common path prefix")) + "\n            ")]), _vm._v(" "), _c(_setup.NcActionCheckbox, {
    ref: "mountBackgroundJobComponent",
    attrs: {
      checked: _setup.archiveMountBackgroundJob
    },
    on: {
      change: function ($event) {
        _setup.archiveMountBackgroundJob = !_setup.archiveMountBackgroundJob;
      }
    }
  }, [_vm._v("\n              " + _vm._s(_setup.t(_setup.appName, "schedule as background job")) + "\n            ")])], 1)], 1)], 1)]), _vm._v(" "), _c("li", {
    staticClass: "files-tab-entry flex flex-center clickable",
    on: {
      click: function ($event) {
        _setup.showArchiveExtraction = !_setup.showArchiveExtraction;
      }
    }
  }, [_c("div", {
    staticClass: "files-tab-entry__avatar icon-play-white"
  }), _vm._v(" "), _c("div", {
    staticClass: "files-tab-entry__desc"
  }, [_c("h5", [_vm._v(_vm._s(_setup.t(_setup.appName, "Extract Archive")))])]), _vm._v(" "), _c(_setup.NcActions, [_c(_setup.NcActionButton, {
    attrs: {
      icon: "icon-triangle-" + (_setup.showArchiveExtraction ? "n" : "s")
    },
    model: {
      value: _setup.showArchiveExtraction,
      callback: function ($$v) {
        _setup.showArchiveExtraction = $$v;
      },
      expression: "showArchiveExtraction"
    }
  })], 1)], 1), _vm._v(" "), _c("li", {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: _setup.showArchiveExtraction,
      expression: "showArchiveExtraction"
    }],
    staticClass: "directory-chooser files-tab-entry"
  }, [_setup.loading ? _c("div", {
    staticClass: "icon-loading-small"
  }) : _c("div", [_c(_setup.FilePrefixPicker, {
    attrs: {
      hint: _setup.t(_setup.appName, "Choose a directory to extract the archive to:"),
      placeholder: _setup.t(_setup.appName, "basename")
    },
    on: {
      submit: _setup.extractArchive
    },
    model: {
      value: _setup.archiveExtractFileInfo,
      callback: function ($$v) {
        _setup.archiveExtractFileInfo = $$v;
      },
      expression: "archiveExtractFileInfo"
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "flex flex-center"
  }, [_c("div", {
    staticClass: "label",
    on: {
      click: _setup.openExtractionOptionsMenu
    }
  }, [_vm._v("\n            " + _vm._s(_setup.t(_setup.appName, "Extraction Options")) + "\n          ")]), _vm._v(" "), _c(_setup.NcActions, {
    ref: "extractionOptionsComponent",
    attrs: {
      "force-menu": true
    }
  }, [_c(_setup.NcActionCheckbox, {
    attrs: {
      checked: _setup.archiveExtractStripCommonPathPrefix
    },
    on: {
      change: function ($event) {
        _setup.archiveExtractStripCommonPathPrefix = !_setup.archiveExtractStripCommonPathPrefix;
      }
    }
  }, [_vm._v("\n              " + _vm._s(_setup.t(_setup.appName, "strip common path prefix")) + "\n            ")]), _vm._v(" "), _c(_setup.NcActionCheckbox, {
    ref: "extractBackgroundJobComponent",
    attrs: {
      checked: _setup.archiveExtractBackgroundJob
    },
    on: {
      change: function ($event) {
        _setup.archiveExtractBackgroundJob = !_setup.archiveExtractBackgroundJob;
      }
    }
  }, [_vm._v("\n              " + _vm._s(_setup.t(_setup.appName, "schedule as background job")) + "\n            ")])], 1)], 1)], 1)]), _vm._v(" "), _c("li", {
    staticClass: "files-tab-entry flex flex-center clickable",
    on: {
      click: function ($event) {
        _setup.showPendingJobs = !_setup.showPendingJobs;
      }
    }
  }, [_c("div", {
    staticClass: "files-tab-entry__avatar icon-recent-white"
  }), _vm._v(" "), _c("div", {
    staticClass: "files-tab-entry__desc"
  }, [_setup.jobsArePending ? _c("h5", [_c("span", {
    staticClass: "main-title"
  }, [_vm._v(_vm._s(_setup.t(_setup.appName, "Pending Background Jobs")))]), _vm._v(" "), _setup.jobsArePending ? _c("span", {
    staticClass: "title-annotation"
  }, [_vm._v("(" + _vm._s("" + Object.keys(_setup.pendingJobs).length) + ")")]) : _vm._e()]) : _c("h5", [_c("span", {
    staticClass: "main-title"
  }, [_vm._v(_vm._s(_setup.t(_setup.appName, "No Pending Background Jobs")))])])]), _vm._v(" "), _c(_setup.NcActions, [_c(_setup.NcActionButton, {
    attrs: {
      icon: "icon-triangle-" + (_setup.showPendingJobs ? "n" : "s")
    },
    model: {
      value: _setup.showPendingJobs,
      callback: function ($$v) {
        _setup.showPendingJobs = $$v;
      },
      expression: "showPendingJobs"
    }
  })], 1)], 1), _vm._v(" "), _c("li", {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: _setup.jobsArePending && _setup.showPendingJobs,
      expression: "jobsArePending && showPendingJobs"
    }],
    staticClass: "directory-chooser files-tab-entry"
  }, [_setup.loading ? _c("div", {
    staticClass: "icon-loading-small"
  }) : _setup.jobsArePending ? _c("ul", {
    staticClass: "pending-jobs"
  }, _vm._l(_setup.pendingJobs, function (job) {
    return _c(_setup.NcListItem, {
      key: job.destinationPath,
      attrs: {
        "force-display-actions": true,
        bold: false
      },
      scopedSlots: _vm._u([{
        key: "title",
        fn: function () {
          return [_c("div", [_vm._v(_vm._s(job.destinationPath))])];
        },
        proxy: true
      }, {
        key: "actions",
        fn: function () {
          return [_c(_setup.NcActionButton, {
            on: {
              click: function ($event) {
                return _setup.cancelPendingOperation(job.target);
              }
            },
            scopedSlots: _vm._u([{
              key: "icon",
              fn: function () {
                return [_c(_setup.CancelIcon, {
                  directives: [{
                    name: "tooltip",
                    rawName: "v-tooltip",
                    value: _setup.t(_setup.appName, "Cancel Job"),
                    expression: "t(appName, 'Cancel Job')"
                  }],
                  attrs: {
                    size: 20
                  }
                })];
              },
              proxy: true
            }], null, true)
          })];
        },
        proxy: true
      }, job.stripCommonPathPrefix ? {
        key: "extra",
        fn: function () {
          return [_c("div", [_vm._v(_vm._s(_setup.t(_setup.appName, "Job type: {type}", {
            type: job.target === "mount" ? _setup.t(_setup.appName, "mount") : _setup.t(_setup.appName, "extract")
          })))]), _vm._v(" "), _c("div", [_vm._v(_vm._s(_setup.t(_setup.appName, "Common prefix {prefix} will be stripped.", {
            prefix: _setup.commonPathPrefix
          })))])];
        },
        proxy: true
      } : null], null, true)
    });
  }), 1) : _c("div", [_vm._v("\n        " + _vm._s(_setup.t(_setup.appName, "No pending background job.")) + "\n      ")])])])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ },

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true"
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./src/views/FilesTab.vue"
/*!********************************!*\
  !*** ./src/views/FilesTab.vue ***!
  \********************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true */ "./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true");
/* harmony import */ var _FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FilesTab.vue?vue&type=script&setup=true&lang=ts */ "./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts");
/* harmony import */ var _FilesTab_vue_vue_type_style_index_0_id_d56e0e50_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true */ "./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");



;


/* normalize component */

var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__["default"],
  _FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render,
  _FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  "d56e0e50",
  null
  
)

/* hot reload */
if (false) // removed by dead control flow
{ var api; }
component.options.__file = "src/views/FilesTab.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ },

/***/ "./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts"
/*!*******************************************************************!*\
  !*** ./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts ***!
  \*******************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_lib_index_js_vue_loader_options_FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js!../../node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!../../node_modules/vue-loader/lib/index.js??vue-loader-options!./FilesTab.vue?vue&type=script&setup=true&lang=ts */ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_lib_index_js_vue_loader_options_FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ },

/***/ "./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true"
/*!**************************************************************************!*\
  !*** ./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true ***!
  \**************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_lib_index_js_vue_loader_options_FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   staticRenderFns: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_lib_index_js_vue_loader_options_FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true__WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_vue_loader_lib_loaders_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_lib_index_js_vue_loader_options_FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js!../../node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!../../node_modules/vue-loader/lib/index.js??vue-loader-options!./FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/loaders/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true");


/***/ },

/***/ "./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true"
/*!*****************************************************************************************!*\
  !*** ./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true ***!
  \*****************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_3_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_FilesTab_vue_vue_type_style_index_0_id_d56e0e50_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/mini-css-extract-plugin/dist/loader.js!../../node_modules/css-loader/dist/cjs.js!../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!../../node_modules/vue-loader/lib/index.js??vue-loader-options!./FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true");


/***/ },

/***/ "?6abd"
/*!************************!*\
  !*** buffer (ignored) ***!
  \************************/
() {

/* (ignored) */

/***/ },

/***/ "?c69c"
/*!************************!*\
  !*** crypto (ignored) ***!
  \************************/
() {

/* (ignored) */

/***/ }

}]);
//# sourceMappingURL=src_files-tab_ts-53ffffc88a4bc7193342.js.map