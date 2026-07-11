(self["webpackChunkfiles_archive"] = self["webpackChunkfiles_archive"] || []).push([["src_views_FilesTab_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts"
/*!******************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm-bundler.js");
/* harmony import */ var _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @nextcloud/dialogs */ "./node_modules/@nextcloud/dialogs/dist/index.mjs");
/* harmony import */ var _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @nextcloud/l10n */ "./node_modules/@nextcloud/l10n/dist/index.mjs");
/* harmony import */ var _nextcloud_paths__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @nextcloud/paths */ "./node_modules/@nextcloud/paths/dist/index.mjs");
/* harmony import */ var _TextFieldWithSubmitButton_vue__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./TextFieldWithSubmitButton.vue */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue");
/* harmony import */ var _config_ts__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../config.ts */ "./repositories/rotdrop/nextcloud-vue-components/lib/config.ts");
/* harmony import */ var _directives_Tooltip_index_ts__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../directives/Tooltip/index.ts */ "./repositories/rotdrop/nextcloud-vue-components/lib/directives/Tooltip/index.ts");
/* harmony import */ var _nextcloud_dialogs_style_css__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @nextcloud/dialogs/style.css */ "./node_modules/@nextcloud/dialogs/dist/style.css");








// @ts-expect-error 2882 Blah.
 // still needed?
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (/*@__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.defineComponent)({
  ...{
    name: 'FilePrefixPicker'
  },
  __name: 'FilePrefixPicker',
  props: {
    modelValue: {
      type: Object,
      required: false,
      default: () => {
        return {
          baseName: '',
          dirName: ''
        };
      }
    },
    baseName: {
      type: String,
      required: false,
      default: undefined
    },
    dirName: {
      type: String,
      required: false,
      default: undefined
    },
    onlyDirName: {
      type: Boolean,
      required: false,
      default: false
    },
    hint: {
      type: String,
      required: false,
      default: undefined
    },
    placeholder: {
      type: String,
      required: false,
      default: undefined
    },
    readonly: {
      type: [Boolean, String],
      required: false,
      default: undefined
    },
    disabled: {
      type: Boolean,
      required: false,
      default: undefined
    },
    filePickerTitle: {
      type: String,
      required: false,
      default: undefined
    }
  },
  emits: ['input', 'submit', 'error:invalidDirName', 'update:dirName', 'update:modelValue', 'update:value'],
  setup(__props, {
    expose: __expose,
    emit: __emit
  }) {
    __expose();
    const props = __props;
    const emit = __emit;
    const pathInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.reactive)(props.modelValue);
    const pathName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => (pathInfo.dirName ? pathInfo.dirName + '/' : '') + (props.onlyDirName ? '' : pathInfo.baseName));
    const displayDirName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => !pathInfo.dirName ? './' : pathInfo.dirName + (pathInfo.dirName !== '/' ? '/' : ''));
    const filePickerTitle = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => props.filePickerTitle || (props.onlyDirName ? (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_2__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_5__.appName, 'Choose a folder') : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_2__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_5__.appName, 'Choose a prefix-folder')));
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(pathName, () => {
      emit('update:modelValue', pathInfo);
      emit('update:value', pathInfo);
      emit('input', pathInfo);
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.onBeforeMount)(() => {
      pathInfo.dirName = props.modelValue?.dirName || '';
      pathInfo.baseName = props.modelValue?.baseName || '';
      if (!pathInfo.baseName && props.baseName) {
        pathInfo.baseName = props.baseName;
      }
      if (!pathInfo.dirName && props.dirName) {
        pathInfo.dirName = props.dirName;
      }
      if (!pathInfo.dirName) {
        pathInfo.dirName = '/';
      }
    });
    const openFilePicker = async () => {
      const picker = (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_1__.getFilePickerBuilder)(filePickerTitle.value).startAt(pathInfo.dirName).setMultiSelect(false).setButtonFactory((nodes, path) => {
        const node = nodes[0];
        const target = node?.displayname || (0,_nextcloud_paths__WEBPACK_IMPORTED_MODULE_3__.basename)(path);
        const label = nodes.length === 1 ? (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_2__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_5__.appName, 'Choose {file}', {
          file: target
        }) : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_2__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_5__.appName, 'Choose');
        return [{
          callback: () => {},
          label,
          variant: 'primary'
        }];
      }).setMimeTypeFilter(['httpd/unix-directory']).allowDirectories().build();
      let dir = (await picker.pick()) || '/';
      if (Array.isArray(dir)) {
        // work around bug in @nextcloud/dialogs@6.2.0
        dir = dir[0];
      }
      if (dir.startsWith('//')) {
        // new in Nextcloud 25?
        dir = dir.slice(1);
      }
      if (!dir.startsWith('/')) {
        (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_1__.showError)((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_2__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_5__.appName, 'Invalid path selected: "{dir}".', {
          dir
        }), {
          timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_1__.TOAST_PERMANENT_TIMEOUT
        });
        emit('error:invalidDirName', dir);
      } else {
        if (dir === '/') {
          dir = '';
        }
        (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_1__.showInfo)((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_2__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_5__.appName, 'Selected path: "{dir}/{base}/".', {
          dir,
          base: pathInfo.baseName
        }));
        emit('update:dirName', dir, pathInfo.baseName);
        pathInfo.dirName = dir;
        if (props.onlyDirName) {
          emit('submit', pathInfo);
        }
      }
    };
    _directives_Tooltip_index_ts__WEBPACK_IMPORTED_MODULE_6__.options.themes['unclipped-tooltip'] = {
      $extend: 'tooltip'
    };
    const unclippedPopup = (content, html = true) => {
      return {
        content,
        preventOverflow: true,
        html,
        // shown: true,
        // triggers: [],
        theme: 'unclipped-tooltip'
      };
    };
    const __returned__ = {
      props,
      emit,
      pathInfo,
      pathName,
      displayDirName,
      filePickerTitle,
      openFilePicker,
      unclippedPopup,
      TextFieldWithSubmitButton: _TextFieldWithSubmitButton_vue__WEBPACK_IMPORTED_MODULE_4__["default"],
      get vTooltip() {
        return _directives_Tooltip_index_ts__WEBPACK_IMPORTED_MODULE_6__["default"];
      }
    };
    Object.defineProperty(__returned__, '__isScriptSetup', {
      enumerable: false,
      value: true
    });
    return __returned__;
  }
}));

/***/ },

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts"
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm-bundler.js");
/* harmony import */ var _nextcloud_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @nextcloud/vue */ "./node_modules/@nextcloud/vue/dist/index.mjs");



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (/*@__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.defineComponent)({
  ...{
    name: 'TextFieldWithSubmitButton',
    inheritAttrs: false
  },
  __name: 'TextFieldWithSubmitButton',
  props: {
    modelValue: {
      type: [String, Number],
      required: false,
      default: undefined
    },
    value: {
      type: [String, Number],
      required: false,
      default: undefined
    },
    hint: {
      type: String,
      required: false,
      default: undefined
    },
    flexContainerClasses: {
      type: Array,
      required: false,
      default: () => ['flex-justify-left', 'flex-align-start']
    },
    flexItemClasses: {
      type: Array,
      required: false,
      default: () => ['flex-justify-left', 'flex-align-start']
    }
  },
  emits: ['submit', 'input', 'update:modelValue', 'update:value'],
  setup(__props, {
    expose: __expose,
    emit: __emit
  }) {
    __expose();
    const props = __props;
    const emit = __emit;
    // Keep a private data of the copy in order to support even missing
    // value or modelValue props. Still hitting the submit button should
    // present the current input value as event data.
    const model = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(props.modelValue || props.value || '');
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(() => props.value, value => {
      model.value = value;
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(() => props.modelValue, value => {
      model.value = value;
    });
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(model, value => {
      emit('update:modelValue', value);
      emit('update:value', value);
      emit('input', value);
    });
    const __returned__ = {
      props,
      emit,
      model,
      get NcTextField() {
        return _nextcloud_vue__WEBPACK_IMPORTED_MODULE_1__.NcTextField;
      }
    };
    Object.defineProperty(__returned__, '__isScriptSetup', {
      enumerable: false,
      value: true
    });
    return __returned__;
  }
}));

/***/ },

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts"
/*!*******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts ***!
  \*******************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm-bundler.js");
/* harmony import */ var _nextcloud_auth__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @nextcloud/auth */ "./node_modules/@nextcloud/auth/dist/index.mjs");
/* harmony import */ var _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @nextcloud/axios */ "./node_modules/@nextcloud/axios/dist/index.js");
/* harmony import */ var _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @nextcloud/dialogs */ "./node_modules/@nextcloud/dialogs/dist/index.mjs");
/* harmony import */ var _nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @nextcloud/event-bus */ "./node_modules/@nextcloud/event-bus/dist/index.mjs");
/* harmony import */ var _nextcloud_files__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @nextcloud/files */ "./node_modules/@nextcloud/files/dist/index.mjs");
/* harmony import */ var _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @nextcloud/l10n */ "./node_modules/@nextcloud/l10n/dist/index.mjs");
/* harmony import */ var _nextcloud_router__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @nextcloud/router */ "./node_modules/@nextcloud/router/dist/index.mjs");
/* harmony import */ var _nextcloud_vue__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @nextcloud/vue */ "./node_modules/@nextcloud/vue/dist/index.mjs");
/* harmony import */ var _rotdrop_nextcloud_vue_components_lib_directives_Tooltip__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @rotdrop/nextcloud-vue-components/lib/directives/Tooltip */ "./repositories/rotdrop/nextcloud-vue-components/lib/directives/Tooltip/index.ts");
/* harmony import */ var js_md5__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! js-md5 */ "./node_modules/js-md5/src/md5.js");
/* harmony import */ var js_md5__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(js_md5__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _rotdrop_nextcloud_vue_components_lib_components_FilePrefixPicker_vue__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue");
/* harmony import */ var vue_material_design_icons_Cancel_vue__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! vue-material-design-icons/Cancel.vue */ "./node_modules/vue-material-design-icons/Cancel.vue");
/* harmony import */ var vue_material_design_icons_NetworkOff_vue__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! vue-material-design-icons/NetworkOff.vue */ "./node_modules/vue-material-design-icons/NetworkOff.vue");
/* harmony import */ var _config_ts__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../config.ts */ "./src/config.ts");
/* harmony import */ var _console_ts__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../console.ts */ "./src/console.ts");
/* harmony import */ var _toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../toolkit/types/axios-type-guards.ts */ "./src/toolkit/types/axios-type-guards.ts");
/* harmony import */ var _toolkit_util_file_node_busy_indicator_ts__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../toolkit/util/file-node-busy-indicator.ts */ "./src/toolkit/util/file-node-busy-indicator.ts");
/* harmony import */ var _toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ../toolkit/util/file-node-helper.ts */ "./src/toolkit/util/file-node-helper.ts");
/* harmony import */ var _toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ../toolkit/util/generate-url.ts */ "./src/toolkit/util/generate-url.ts");
/* harmony import */ var _toolkit_util_initial_state_ts__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ../toolkit/util/initial-state.ts */ "./src/toolkit/util/initial-state.ts");






















const ArchiveStatusOk = 0;
const ArchiveStatusTooLarge = 1;
const ArchiveStatusBomb = 2;
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (/*@__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.defineComponent)({
  __name: 'FilesTab',
  props: {
    node: {
      type: Object,
      required: true
    },
    folder: {
      type: Object,
      required: false,
      default: undefined
    },
    view: {
      type: Object,
      required: false,
      default: undefined
    }
  },
  setup(__props, {
    expose: __expose
  }) {
    const props = __props;
    _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('PROPS', {
      props,
      node: {
        path: props.node.path,
        basename: props.node.basename,
        dirname: props.node.dirname
      }
    });
    const setBusyState = state => {
      (0,_toolkit_util_file_node_busy_indicator_ts__WEBPACK_IMPORTED_MODULE_17__.setFileNodeBusy)(props.node, state);
    };
    setBusyState(false); // needs to be done once while in setup mode
    const archivePassPhraseComponent = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(null);
    const mountOptionsComponent = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(null);
    const extractionOptionsComponent = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(null);
    const loading = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(0);
    const fileName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => props.node ? props.node.path : null);
    const archiveFileId = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => props.node?.id);
    const archiveInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(undefined);
    const archiveStatus = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(undefined);
    const archiveMounts = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)([]);
    const pendingJobs = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({});
    const jobsArePending = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => Object.keys(pendingJobs.value).length > 0);
    const initialState = (0,_toolkit_util_initial_state_ts__WEBPACK_IMPORTED_MODULE_20__["default"])();
    const archiveMountStripCommonPathPrefix = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!initialState?.mountStripCommonPathPrefixDefault);
    const archiveExtractStripCommonPathPrefix = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!initialState?.extractStripCommonPathPrefixDefault);
    const archiveMountBackgroundJob = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!initialState?.mountBackgroundJob);
    const archiveExtractBackgroundJob = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(!!initialState?.extractBackgroundJob);
    const archivePassPhrase = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(undefined);
    const showArchiveInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(true);
    const showArchiveMounts = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    const showArchiveExtraction = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    const showPendingJobs = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)(false);
    const openMountTarget = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => (0,js_md5__WEBPACK_IMPORTED_MODULE_10__.md5)((0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_7__.generateUrl)('') + _config_ts__WEBPACK_IMPORTED_MODULE_14__.appName + '-open-archive-mount'));
    const archiveMountFileInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({
      dirName: '',
      baseName: ''
    });
    const archiveExtractFileInfo = (0,vue__WEBPACK_IMPORTED_MODULE_0__.ref)({
      dirName: '',
      baseName: ''
    });
    const archiveMountBaseName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveMountFileInfo.value.baseName;
      },
      set(value) {
        archiveMountFileInfo.value.baseName = value;
        return value;
      }
    });
    const archiveMountDirName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveMountFileInfo.value.dirName;
      },
      set(value) {
        archiveMountFileInfo.value.dirName = value;
        return value;
      }
    });
    const archiveExtractBaseName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveExtractFileInfo.value.baseName;
      },
      set(value) {
        archiveExtractFileInfo.value.baseName = value;
        return value;
      }
    });
    const archiveExtractDirName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)({
      get() {
        return archiveExtractFileInfo.value.dirName;
      },
      set(value) {
        archiveExtractFileInfo.value.dirName = value;
        return value;
      }
    });
    const archiveMountPathName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => archiveMountDirName.value + (archiveMountBaseName.value ? '/' + archiveMountBaseName.value : ''));
    const archiveExtractPathName = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => archiveExtractDirName.value + (archiveExtractBaseName.value ? '/' + archiveExtractBaseName.value : ''));
    const archiveStatusText = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      if (archiveStatus.value === ArchiveStatusOk) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'ok');
      } else if (archiveStatus.value & ArchiveStatusBomb) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'zip bomb');
      } else if (archiveStatus.value & ArchiveStatusTooLarge) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'too large');
      }
      return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'unknown');
    });
    const archiveMounted = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => archiveMounts.value.length > 0);
    // const archiveInfoText = computed(() => JSON.stringify(archiveInfo.value, null, 2))
    const humanArchiveOriginalSize = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => !isNaN(parseInt('' + archiveInfo.value?.originalSize)) ? (0,_nextcloud_files__WEBPACK_IMPORTED_MODULE_5__.formatFileSize)(archiveInfo.value.originalSize) : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'unknown'));
    const humanArchiveCompressedSize = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => !isNaN(parseInt('' + archiveInfo.value?.compressedSize)) ? (0,_nextcloud_files__WEBPACK_IMPORTED_MODULE_5__.formatFileSize)(archiveInfo.value.compressedSize) : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'unknown'));
    const humanArchiveFileSize = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => !isNaN(parseInt('' + archiveInfo.value?.size)) ? (0,_nextcloud_files__WEBPACK_IMPORTED_MODULE_5__.formatFileSize)(archiveInfo.value.size) : (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'unknown'));
    const numberOfArchiveMembers = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => {
      if (!archiveInfo.value || archiveInfo.value?.numberOfFiles === undefined || isNaN(parseInt('' + archiveInfo.value?.numberOfFiles))) {
        return (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'unknown');
      }
      return '' + archiveInfo.value?.numberOfFiles;
    });
    const commonPathPrefix = (0,vue__WEBPACK_IMPORTED_MODULE_0__.computed)(() => !archiveInfo.value || archiveInfo.value.commonPathPrefix === undefined ? (0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'unknown') : '/' + archiveInfo.value.commonPathPrefix);
    // const mountPointTitle = computed(() =>
    //   t(appName, 'Mount Points')
    //     + ' ('
    //     + (archiveMounted ? archiveMounts.value.length : t(appName, 'not mounted'))
    //     + ')'
    // )
    // We ____DO____  want to compare numerically here.
    const isLt = (a, b) => a < b;
    const openMountOptionsMenu = () => {
      mountOptionsComponent.value?.openMenu();
    };
    const openExtractionOptionsMenu = () => {
      extractionOptionsComponent.value?.openMenu();
    };
    /**
     * Fetch some needed data ...
     */
    const getData = async () => {
      archiveMountStripCommonPathPrefix.value = !!initialState?.mountStripCommonPathPrefixDefault;
      archiveExtractStripCommonPathPrefix.value = !!initialState?.extractStripCommonPathPrefixDefault;
      archiveMountBackgroundJob.value = !!initialState?.mountBackgroundJob;
      archiveExtractBackgroundJob.value = !!initialState?.extractBackgroundJob;
      if (!fileName.value) {
        return;
      }
      getArchiveInfo(fileName.value);
      refreshArchiveMounts(fileName.value, true);
      getPendingJobs(fileName.value, true);
    };
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.watch)(() => props.node, async () => {
      _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].debug('Node has changed', {
        node: {
          ...props.node
        },
        folder: {
          ...props.folder
        },
        view: {
          ...props.view
        }
      });
      await update();
    }, {
      immediate: true
    });
    /**
     * Update current fileInfo and fetch new data.
     */
    async function update() {
      /* this.fileList = OCA.Files.App.currentFileList
       * this.fileList.$el.off('updated').on('updated', function(event) {
       *   logger.info('FILE LIST UPDATED, ARGS', arguments)
       * }) */
      archiveMountBaseName.value = props.node.basename.split('.')[0];
      archiveMountDirName.value = props.node.dirname;
      archiveExtractBaseName.value = archiveMountBaseName.value;
      archiveExtractDirName.value = archiveMountDirName.value;
      getData();
    }
    __expose({
      update
    });
    async function getArchiveInfo(fileName) {
      ++loading.value;
      fileName = encodeURIComponent(fileName);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_19__["default"])('archive/info/{fileName}', {
        fileName
      });
      const requestData = {};
      if (archivePassPhrase.value) {
        requestData.passPhrase = archivePassPhrase.value;
      }
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].post(url, requestData);
        const responseData = response.data;
        archiveInfo.value = responseData.archiveInfo;
        archiveStatus.value = responseData.archiveStatus;
        if (responseData.messages) {
          for (const message of responseData.messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showInfo)(message);
          }
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e) && e.response.data) {
          const responseData = e.response.data;
          archiveInfo.value = responseData.archiveInfo;
          archiveStatus.value = responseData.archiveStatus;
          if (responseData.messages) {
            for (const message of responseData.messages) {
              (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
                timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
              });
            }
          }
        } else {
          archiveInfo.value = undefined;
        }
      }
      if (archiveInfo.value?.defaultMountPoint) {
        archiveMountBaseName.value = archiveInfo.value.defaultMountPoint;
      }
      if (archiveInfo.value?.defaultTargetBaseName) {
        archiveExtractBaseName.value = archiveInfo.value.defaultTargetBaseName;
      }
      --loading.value;
    }
    /**
     * @param filename TBD.
     *
     * @param noEmit TBD.
     */
    async function refreshArchiveMounts(filename, noEmit) {
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
        const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_18__.fileInfoToNode)(mount.mountPoint);
        node.attributes['is-mount-root'] = true;
        (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:deleted', node);
      }
      for (const mount of newMounts) {
        const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_18__.fileInfoToNode)(mount.mountPoint);
        node.attributes['is-mount-root'] = true;
        (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:created', node);
      }
    }
    const getJobIdFromOperation = (operation, archivePath, mountPath) => {
      return (0,js_md5__WEBPACK_IMPORTED_MODULE_10__.md5)(operation + archivePath + mountPath);
    };
    const getJobIdFromJob = job => {
      return getJobIdFromOperation(job.target, job.sourcePath, job.destinationPath);
    };
    /**
     * @param fileName TBD.
     *
     * @param silent TBD.
     */
    async function getPendingJobs(fileName, silent) {
      if (silent !== true) {
        ++loading.value;
      }
      fileName = encodeURIComponent(fileName);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_19__["default"])('archive/schedule/{operation}/{fileName}', {
        operation: 'status',
        fileName
      });
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].get(url);
        const responseData = response.data;
        const jobs = {};
        for (const job of responseData) {
          jobs[getJobIdFromJob(job)] = job;
        }
        for (const jobId of Object.keys(pendingJobs.value)) {
          if (!jobs[jobId]) {
            delete pendingJobs.value[jobId];
          }
        }
        for (const [jobId, job] of Object.entries(jobs)) {
          pendingJobs.value[jobId] = job;
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e) && e.response.data) {
          const responseData = e.response.data;
          if (responseData.messages) {
            for (const message of responseData.messages) {
              (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
                timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
              });
            }
          }
        }
      }
      if (silent !== true) {
        --loading.value;
      }
    }
    const cancelPendingOperation = async operation => {
      const archivePath = encodeURIComponent(fileName.value);
      const mountPath = encodeURIComponent(archiveMountPathName.value);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_19__["default"])('archive/schedule/{operation}/{archivePath}/{mountPath}', {
        operation,
        archivePath,
        mountPath
      });
      let responseData = {};
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].delete(url, {});
        responseData = response.data;
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            responseData = e.response.data;
            if (Array.isArray(responseData.messages)) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'Cancelling the background job failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      if (responseData.removed) {
        for (const job of responseData.removed) {
          const jobId = getJobIdFromJob(job);
          if (pendingJobs.value[jobId]) {
            delete pendingJobs.value[jobId];
          }
        }
      }
    };
    /**
     * @param fileName TBD.
     *
     * @param silent TBD.
     */
    async function getArchiveMounts(fileName, silent) {
      const result = {
        mounts: [],
        mounted: false
      };
      if (silent !== true) {
        ++loading.value;
      }
      fileName = encodeURIComponent(fileName);
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_19__["default"])('archive/mount/{fileName}', {
        fileName
      });
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].get(url);
        const responseData = response.data;
        result.mounts = responseData.mounts;
        result.mounted = responseData.mounted;
        if (responseData.messages) {
          for (const message of responseData.messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showInfo)(message);
          }
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e) && e.response.data) {
          const responseData = e.response.data;
          result.mounts = responseData.mounts;
          result.mounted = responseData.mounted;
          if (responseData.messages) {
            for (const message of responseData.messages) {
              (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
                timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
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
    }
    const mountArchive = async () => {
      const archivePath = encodeURIComponent(fileName.value);
      const mountPath = encodeURIComponent(archiveMountPathName.value);
      const urlTemplate = archiveMountBackgroundJob.value ? 'archive/schedule/mount/{archivePath}/{mountPath}' : 'archive/mount/{archivePath}/{mountPath}';
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_19__["default"])(urlTemplate, {
        archivePath,
        mountPath
      });
      setBusyState(true);
      const requestData = {};
      if (archivePassPhrase.value) {
        requestData.passPhrase = archivePassPhrase.value;
      }
      requestData.stripCommonPathPrefix = !!archiveMountStripCommonPathPrefix.value;
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].post(url, requestData);
        if (!archiveMountBackgroundJob.value) {
          const newMount = response.data;
          const newFileId = newMount.mountPoint.fileid;
          if (archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === newFileId) === -1) {
            archiveMounts.value.push(newMount);
            const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_18__.fileInfoToNode)(response.data.mountPoint);
            node.attributes['is-mount-root'] = true;
            (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:created', node);
          }
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'Mount request failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      if (archiveMountBackgroundJob.value) {
        getPendingJobs(fileName.value, true);
      }
      setBusyState(false);
    };
    const unmount = async mount => {
      const cloudUser = (0,_nextcloud_auth__WEBPACK_IMPORTED_MODULE_1__.getCurrentUser)();
      const url = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_7__.generateRemoteUrl)('dav/files/' + cloudUser.uid + mount.mountPointPath);
      setBusyState(true);
      try {
        await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].delete(url);
        const mountIndex = archiveMounts.value.indexOf(mount);
        if (mountIndex >= 0) {
          archiveMounts.value.splice(mountIndex, 1);
        } else {
          _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('UNABLE TO FIND DELETED MOUNT IN LIST', mount, archiveMounts);
        }
        const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_18__.fileInfoToNode)(mount.mountPoint);
        node.attributes['is-mount-root'] = true;
        (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:deleted', node);
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        const messages = [];
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e)) {
          // attempt parsing Sabre exception is available
          const xml = e.response.request?.responseXML;
          if (xml && xml.documentElement.localName === 'error' && xml.documentElement.namespaceURI === 'DAV:') {
            const xmlMessages = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'message');
            // const exceptions = xml.getElementsByTagNameNS('http://sabredav.org/ns', 'exception');
            for (const message of xmlMessages) {
              if (message.textContent) {
                messages.push(message.textContent);
              }
            }
          }
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'Unmount request failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
            });
          }
          if (e.response.status === 404) {
            refreshArchiveMounts(fileName.value, true);
          }
        }
      }
      setBusyState(false);
    };
    const extractArchive = async () => {
      const archivePath = encodeURIComponent(fileName.value);
      const targetPath = encodeURIComponent(archiveExtractPathName.value);
      const urlTemplate = archiveExtractBackgroundJob.value ? 'archive/schedule/extract/{archivePath}/{targetPath}' : 'archive/extract/{archivePath}/{targetPath}';
      const url = (0,_toolkit_util_generate_url_ts__WEBPACK_IMPORTED_MODULE_19__["default"])(urlTemplate, {
        archivePath,
        targetPath
      });
      setBusyState(true);
      const requestData = {};
      if (archivePassPhrase.value) {
        requestData.passPhrase = archivePassPhrase.value;
      }
      requestData.stripCommonPathPrefix = !!archiveExtractStripCommonPathPrefix.value;
      try {
        const response = await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].post(url, requestData);
        if (!archiveExtractBackgroundJob.value) {
          const node = (0,_toolkit_util_file_node_helper_ts__WEBPACK_IMPORTED_MODULE_18__.fileInfoToNode)(response.data.targetFolder);
          node.attributes['is-mount-root'] = true;
          (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_4__.emit)('files:node:created', node);
        }
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'Archive extraction failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      if (archiveExtractBackgroundJob.value) {
        getPendingJobs(fileName.value, true);
      }
      setBusyState(false);
    };
    const setPassPhrase = async () => {
      _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('PASPHRASE', {
        archivePassPhrase
      });
      // patch it into existing mounts if any
      const archivePath = encodeURIComponent(fileName.value);
      const url = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_7__.generateUrl)('/apps/' + _config_ts__WEBPACK_IMPORTED_MODULE_14__.appName + '/archive/mount/{archivePath}', {
        archivePath
      });
      setBusyState(true);
      const requestData = {
        changeSet: {
          archivePassPhrase: archivePassPhrase.value
        }
      };
      try {
        await _nextcloud_axios__WEBPACK_IMPORTED_MODULE_2__["default"].patch(url, requestData);
      } catch (e) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('ERROR', e);
        if ((0,_toolkit_types_axios_type_guards_ts__WEBPACK_IMPORTED_MODULE_16__.isAxiosErrorResponse)(e)) {
          const messages = [];
          if (e.response.data) {
            const responseData = e.response.data;
            if (responseData.messages) {
              messages.splice(messages.length, 0, ...responseData.messages);
            }
          }
          if (!messages.length) {
            messages.push((0,_nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate)(_config_ts__WEBPACK_IMPORTED_MODULE_14__.appName, 'Patching the passphrase failed with error {status}, "{statusText}".', {
              status: e.response.status,
              statusText: e.response.statusText
            }));
          }
          for (const message of messages) {
            (0,_nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.showError)(message, {
              timeout: _nextcloud_dialogs__WEBPACK_IMPORTED_MODULE_3__.TOAST_PERMANENT_TIMEOUT
            });
          }
        }
      }
      setBusyState(false);
    };
    const filesAppMountPointUrl = mountPoint => {
      return (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_7__.generateUrl)('/apps/files') + '?dir=' + encodeURIComponent(mountPoint.mountPointPath);
    };
    const onNotification = event => {
      if (event?.notification?.app !== _config_ts__WEBPACK_IMPORTED_MODULE_14__.appName) {
        return;
      }
      _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('APP NOTIFICATION RECEIVED', {
        event
      });
      const destinationData = event?.notification?.messageRichParameters?.destination;
      switch (destinationData?.status) {
        case 'mount':
          {
            const mountData = destinationData?.mount;
            if (!mountData) {
              _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('No mount info in mount notification event');
              return;
            }
            let mount;
            try {
              mount = JSON.parse(mountData);
            } catch (error) {
              _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('files_archive, unable to decode mount entity', {
                event,
                mountData,
                error
              });
              return;
            }
            if (mount.archiveFileId !== archiveFileId.value) {
              // not for us, in the future we may want to maintain a store
              // and cache the data for all file-ids.
              _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('*** Archive notification for other file received', event);
              return;
            }
            const jobId = getJobIdFromOperation('mount', mount.archiveFilePath, mount.mountPointPath);
            if (pendingJobs.value[jobId]) {
              delete pendingJobs.value[jobId];
            }
            _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('*** Mount notification received, updating mount-list', destinationData);
            const mountFileId = destinationData.id;
            const mountIndex = archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === mountFileId);
            if (mountIndex === -1) {
              try {
                archiveMounts.value.push({
                  ...mount,
                  mountPoint: JSON.parse(destinationData.folder)
                });
              } catch (error) {
                _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].error('Unable to decode mount point folder file-info record.', {
                  destinationData,
                  error
                });
              }
            }
            break;
          }
        case 'extract':
          _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('EXTRACT, SHOULD DO SOMETHING');
          break;
      }
    };
    const onMountPointRenamed = mountPoint => {
      // update the list of mountpoints
      const mountFileId = mountPoint.id;
      const mountIndex = archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === mountFileId);
      if (mountIndex >= 0) {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('BERFORE RENAME', {
          ...archiveMounts[mountIndex]
        });
        const mount = archiveMounts[mountIndex];
        mount.mountPoint = mountPoint;
        mount.mountPointPath = mountPoint.path;
        mount.mountPointPathHash = (0,js_md5__WEBPACK_IMPORTED_MODULE_10__.md5)(mountPoint.path);
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('AFTER RENAME', {
          ...mount
        });
      } else {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('RENAME OF NODE NOT FOR US', mountPoint);
      }
    };
    const onMountPointDeleted = mountPoint => {
      const mountFileId = mountPoint.id;
      const mountIndex = archiveMounts.value.findIndex(mount => mount.mountPoint.fileid === mountFileId);
      if (mountIndex >= 0) {
        archiveMounts.value.splice(mountIndex, 1);
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('RECORD UNMOUNT', mountPoint);
      } else {
        _console_ts__WEBPACK_IMPORTED_MODULE_15__["default"].info('DELETE OF NODE NOT FOR US', {
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
    const __returned__ = {
      props,
      setBusyState,
      archivePassPhraseComponent,
      mountOptionsComponent,
      extractionOptionsComponent,
      loading,
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
      filesAppMountPointUrl,
      onNotification,
      onMountPointRenamed,
      onMountPointDeleted,
      get t() {
        return _nextcloud_l10n__WEBPACK_IMPORTED_MODULE_6__.translate;
      },
      get NcActionButton() {
        return _nextcloud_vue__WEBPACK_IMPORTED_MODULE_8__.NcActionButton;
      },
      get NcActionCheckbox() {
        return _nextcloud_vue__WEBPACK_IMPORTED_MODULE_8__.NcActionCheckbox;
      },
      get NcActionInput() {
        return _nextcloud_vue__WEBPACK_IMPORTED_MODULE_8__.NcActionInput;
      },
      get NcActions() {
        return _nextcloud_vue__WEBPACK_IMPORTED_MODULE_8__.NcActions;
      },
      get NcListItem() {
        return _nextcloud_vue__WEBPACK_IMPORTED_MODULE_8__.NcListItem;
      },
      get vTooltip() {
        return _rotdrop_nextcloud_vue_components_lib_directives_Tooltip__WEBPACK_IMPORTED_MODULE_9__["default"];
      },
      FilePrefixPicker: _rotdrop_nextcloud_vue_components_lib_components_FilePrefixPicker_vue__WEBPACK_IMPORTED_MODULE_11__["default"],
      CancelIcon: vue_material_design_icons_Cancel_vue__WEBPACK_IMPORTED_MODULE_12__["default"],
      NetworkOffIcon: vue_material_design_icons_NetworkOff_vue__WEBPACK_IMPORTED_MODULE_13__["default"],
      get appName() {
        return _config_ts__WEBPACK_IMPORTED_MODULE_14__.appName;
      }
    };
    Object.defineProperty(__returned__, '__isScriptSetup', {
      enumerable: false,
      value: true
    });
    return __returned__;
  }
}));

/***/ },

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true"
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm-bundler.js");

const _hoisted_1 = {
  class: "input-wrapper"
};
const _hoisted_2 = {
  key: 0,
  class: "hint"
};
const _hoisted_3 = {
  class: "flex flex-center flex-wrap"
};
const _hoisted_4 = {
  class: "dirname"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [$props.hint ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_2, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.hint), 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)(((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
    href: "#",
    class: "file-picker button icon-folder",
    onClick: _cache[0] || (_cache[0] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(() => !$props.disabled && $setup.openFilePicker(), ["prevent", "stop"]))
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.displayDirName), 1 /* TEXT */)])), [[$setup["vTooltip"], $setup.unclippedPopup($setup.pathInfo.dirName)]]), !$props.onlyDirName ? (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)(((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup["TextFieldWithSubmitButton"], {
    key: 0,
    modelValue: $setup.pathInfo.baseName,
    "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => $setup.pathInfo.baseName = $event),
    label: $props.placeholder,
    class: "flex-grow",
    placeholder: $props.placeholder,
    readonly: $props.readonly === 'basename',
    disabled: $props.disabled,
    onSubmit: _cache[2] || (_cache[2] = $event => $setup.emit('submit', $setup.pathInfo))
  }, null, 8 /* PROPS */, ["modelValue", "label", "placeholder", "readonly", "disabled"])), [[$setup["vTooltip"], $setup.unclippedPopup($setup.pathInfo.baseName)]]) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])]);
}

/***/ },

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true"
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm-bundler.js");

const _hoisted_1 = {
  class: "component-wrapper"
};
const _hoisted_2 = {
  key: 0,
  class: "hint"
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["alignment-wrapper", [...$setup.props.flexContainerClasses]])
  }, [_ctx.$slots.alignedBefore ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    key: 0,
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["aligned-before", [...$setup.props.flexItemClasses]])
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "alignedBefore", {}, undefined, true)], 2 /* CLASS */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcTextField"], (0,vue__WEBPACK_IMPORTED_MODULE_0__.mergeProps)({
    modelValue: $setup.model,
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => $setup.model = $event)
  }, _ctx.$attrs, {
    showTrailingButton: true,
    trailingButtonIcon: "arrowEnd",
    onTrailingButtonClick: _cache[1] || (_cache[1] = $event => _ctx.$emit('submit', $setup.model))
  }), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createSlots)({
    _: 2 /* DYNAMIC */
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(_ctx.$slots, (_, slotName) => {
    return {
      name: slotName,
      fn: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(slotData => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, slotName, (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeProps)((0,vue__WEBPACK_IMPORTED_MODULE_0__.guardReactiveProps)(slotData)), undefined, true)])
    };
  })]), 1040 /* FULL_PROPS, DYNAMIC_SLOTS */, ["modelValue"]), _ctx.$slots.alignedAfter ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    key: 1,
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["aligned-after", [...$props.flexItemClasses]])
  }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "alignedAfter", {}, undefined, true)], 2 /* CLASS */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)], 2 /* CLASS */), $props.hint !== '' || !!_ctx.$slots.hint ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("p", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.hint) + " ", 1 /* TEXT */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "hint", {}, undefined, true)])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)]);
}

/***/ },

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true"
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.runtime.esm-bundler.js");

const _hoisted_1 = {
  class: "files-tab"
};
const _hoisted_2 = {
  class: "files-tab-entry__desc"
};
const _hoisted_3 = {
  class: "files-tab-entry"
};
const _hoisted_4 = {
  key: 0,
  class: "icon-loading-small"
};
const _hoisted_5 = {
  class: "archive-info"
};
const _hoisted_6 = {
  class: "files-tab-entry flex flex-center"
};
const _hoisted_7 = {
  class: "files-tab-entry__desc"
};
const _hoisted_8 = {
  class: "main-title"
};
const _hoisted_9 = {
  key: 0,
  class: "title-annotation"
};
const _hoisted_10 = {
  class: "files-tab-entry__desc"
};
const _hoisted_11 = {
  class: "main-title"
};
const _hoisted_12 = {
  key: 0,
  class: "title-annotation"
};
const _hoisted_13 = {
  key: 1,
  class: "title-annotation"
};
const _hoisted_14 = {
  class: "directory-chooser files-tab-entry"
};
const _hoisted_15 = {
  key: 0,
  class: "icon-loading-small"
};
const _hoisted_16 = {
  key: 1,
  class: "archive-mounts"
};
const _hoisted_17 = ["target", "href"];
const _hoisted_18 = {
  key: 2
};
const _hoisted_19 = {
  class: "flex flex-center"
};
const _hoisted_20 = {
  class: "files-tab-entry__desc"
};
const _hoisted_21 = {
  class: "directory-chooser files-tab-entry"
};
const _hoisted_22 = {
  key: 0,
  class: "icon-loading-small"
};
const _hoisted_23 = {
  key: 1
};
const _hoisted_24 = {
  class: "flex flex-center"
};
const _hoisted_25 = {
  class: "files-tab-entry__desc"
};
const _hoisted_26 = {
  key: 0
};
const _hoisted_27 = {
  class: "main-title"
};
const _hoisted_28 = {
  key: 0,
  class: "title-annotation"
};
const _hoisted_29 = {
  key: 1
};
const _hoisted_30 = {
  class: "main-title"
};
const _hoisted_31 = {
  class: "directory-chooser files-tab-entry"
};
const _hoisted_32 = {
  key: 0,
  class: "icon-loading-small"
};
const _hoisted_33 = {
  key: 1,
  class: "pending-jobs"
};
const _hoisted_34 = {
  key: 2
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", {
    class: "files-tab-entry flex flex-center clickable",
    onClick: _cache[2] || (_cache[2] = $event => $setup.showArchiveInfo = !$setup.showArchiveInfo)
  }, [_cache[23] || (_cache[23] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: "files-tab-entry__avatar icon-info-white"
  }, null, -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Archive Information')), 1 /* TEXT */)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActions"], null, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionButton"], {
      modelValue: $setup.showArchiveInfo,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => $setup.showArchiveInfo = $event),
      icon: 'icon-triangle-' + ($setup.showArchiveInfo ? 'n' : 's'),
      onClick: _cache[1] || (_cache[1] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(() => {}, ["stop"]))
    }, null, 8 /* PROPS */, ["modelValue", "icon"])]),
    _: 1 /* STABLE */
  })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", _hoisted_3, [$setup.loading ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_4)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("ul", _hoisted_5, [$setup.isLt(0, $setup.archiveStatus) ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup["NcListItem"], {
    key: 0,
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({
      'archive-error': $setup.isLt(0, $setup.archiveStatus)
    }),
    name: $setup.t($setup.appName, 'archive status'),
    bold: true,
    details: $setup.archiveStatusText
  }, {
    icon: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [...(_cache[24] || (_cache[24] = [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      class: "icon-error"
    }, null, -1 /* CACHED */)]))]),
    _: 1 /* STABLE */
  }, 8 /* PROPS */, ["class", "name", "details"])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcListItem"], {
    name: $setup.t($setup.appName, 'archive format'),
    bold: true,
    details: $setup.archiveInfo?.format || $setup.t($setup.appName, 'unknown'),
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcListItem"], {
    name: $setup.t($setup.appName, 'MIME type'),
    bold: true,
    details: $setup.archiveInfo?.mimeType || $setup.t($setup.appName, 'unknown'),
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcListItem"], {
    name: $setup.t($setup.appName, 'backend driver'),
    bold: true,
    details: $setup.archiveInfo?.backendDriver || $setup.t($setup.appName, 'unknown'),
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcListItem"], {
    name: $setup.t($setup.appName, 'uncompressed size'),
    bold: true,
    details: $setup.humanArchiveOriginalSize,
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcListItem"], {
    name: $setup.t($setup.appName, 'compressed size'),
    bold: true,
    details: $setup.humanArchiveCompressedSize,
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"]), $setup.humanArchiveCompressedSize !== $setup.humanArchiveFileSize ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup["NcListItem"], {
    key: 1,
    name: $setup.t($setup.appName, 'archive file size'),
    bold: true,
    details: $setup.humanArchiveFileSize,
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcListItem"], {
    name: $setup.t($setup.appName, '# archive members'),
    bold: true,
    details: $setup.numberOfArchiveMembers,
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcListItem"], {
    name: $setup.t($setup.appName, 'common prefix'),
    bold: true,
    details: $setup.commonPathPrefix,
    compact: ""
  }, null, 8 /* PROPS */, ["name", "details"]), $setup.archiveInfo?.comment ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup["NcListItem"], {
    key: 2,
    class: "archive-comment",
    name: $setup.t($setup.appName, 'creator\'s comment'),
    bold: true,
    compact: ""
  }, {
    subtitle: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.archiveInfo?.comment), 1 /* TEXT */)]),
    _: 1 /* STABLE */
  }, 8 /* PROPS */, ["name"])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vShow, !$setup.loading]])], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vShow, $setup.showArchiveInfo]]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", _hoisted_6, [_cache[25] || (_cache[25] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: "files-tab-entry__avatar icon-password-white"
  }, null, -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_7, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_8, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Passphrase')), 1 /* TEXT */), !$setup.archivePassPhrase ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_9, "(" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'unset')) + ")", 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActions"], {
    forceMenu: true
  }, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionInput"], {
      ref: "archivePassPhraseComponent",
      modelValue: $setup.archivePassPhrase,
      "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => $setup.archivePassPhrase = $event),
      type: "password",
      icon: "icon-password",
      onSubmit: $setup.setPassPhrase
    }, {
      default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'archive passphrase')), 1 /* TEXT */)]),
      _: 1 /* STABLE */
    }, 8 /* PROPS */, ["modelValue"])]),
    _: 1 /* STABLE */
  })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", {
    class: "files-tab-entry flex flex-center clickable",
    onClick: _cache[6] || (_cache[6] = $event => $setup.showArchiveMounts = !$setup.showArchiveMounts)
  }, [_cache[26] || (_cache[26] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: "files-tab-entry__avatar icon-external-white"
  }, null, -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_11, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Mount Points')), 1 /* TEXT */), $setup.archiveMounted ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_12, "(" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)('' + $setup.archiveMounts.length) + ")", 1 /* TEXT */)) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_13, "(" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'not mounted')) + ")", 1 /* TEXT */))])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActions"], null, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionButton"], {
      modelValue: $setup.showArchiveMounts,
      "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => $setup.showArchiveMounts = $event),
      icon: 'icon-triangle-' + ($setup.showArchiveMounts ? 'n' : 's'),
      onClick: _cache[5] || (_cache[5] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(() => {}, ["stop"]))
    }, null, 8 /* PROPS */, ["modelValue", "icon"])]),
    _: 1 /* STABLE */
  })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", _hoisted_14, [$setup.loading ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_15)) : $setup.archiveMounted ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("ul", _hoisted_16, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.archiveMounts, mountPoint => {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup["NcListItem"], {
      key: mountPoint.id,
      forceDisplayActions: true,
      bold: false
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createSlots)({
      title: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)(((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
        class: "external icon-folder icon",
        target: $setup.openMountTarget,
        href: $setup.filesAppMountPointUrl(mountPoint)
      }, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(mountPoint.mountPointPath), 1 /* TEXT */)], 8 /* PROPS */, _hoisted_17)), [[$setup["vTooltip"], mountPoint.mountPointPath]])]),
      actions: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionButton"], {
        onClick: $event => $setup.unmount(mountPoint)
      }, {
        icon: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NetworkOffIcon"], {
          size: 20
        }, null, 512 /* NEED_PATCH */), [[$setup["vTooltip"], $setup.t($setup.appName, 'Disconnect storage')]])]),
        _: 1 /* STABLE */
      }, 8 /* PROPS */, ["onClick"])]),
      _: 2 /* DYNAMIC */
    }, [mountPoint.mountFlags & 1 ? {
      name: "extra",
      fn: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Common prefix {prefix} is stripped.', {
        prefix: $setup.commonPathPrefix
      })), 1 /* TEXT */)]),
      key: "0"
    } : undefined]), 1024 /* DYNAMIC_SLOTS */);
  }), 128 /* KEYED_FRAGMENT */))])) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_18, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["FilePrefixPicker"], {
    modelValue: $setup.archiveMountFileInfo,
    "onUpdate:modelValue": _cache[7] || (_cache[7] = $event => $setup.archiveMountFileInfo = $event),
    hint: $setup.t($setup.appName, 'Not mounted, create a new mount point:'),
    placeholder: $setup.t($setup.appName, 'base name'),
    onSubmit: $setup.mountArchive
  }, null, 8 /* PROPS */, ["modelValue", "hint", "placeholder"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_19, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: "label",
    onClick: $setup.openMountOptionsMenu
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Mount Options')), 1 /* TEXT */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActions"], {
    ref: "mountOptionsComponent",
    forceMenu: true
  }, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionCheckbox"], {
      modelValue: $setup.archiveMountStripCommonPathPrefix,
      "onUpdate:modelValue": _cache[8] || (_cache[8] = $event => $setup.archiveMountStripCommonPathPrefix = $event),
      onChange: _cache[9] || (_cache[9] = $event => $setup.archiveMountStripCommonPathPrefix = !$setup.archiveMountStripCommonPathPrefix)
    }, {
      default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'strip common path prefix')), 1 /* TEXT */)]),
      _: 1 /* STABLE */
    }, 8 /* PROPS */, ["modelValue"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionCheckbox"], {
      modelValue: $setup.archiveMountBackgroundJob,
      "onUpdate:modelValue": _cache[10] || (_cache[10] = $event => $setup.archiveMountBackgroundJob = $event),
      onChange: _cache[11] || (_cache[11] = $event => $setup.archiveMountBackgroundJob = !$setup.archiveMountBackgroundJob)
    }, {
      default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'schedule as background job')), 1 /* TEXT */)]),
      _: 1 /* STABLE */
    }, 8 /* PROPS */, ["modelValue"])]),
    _: 1 /* STABLE */
  }, 512 /* NEED_PATCH */)])]))], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vShow, $setup.showArchiveMounts]]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", {
    class: "files-tab-entry flex flex-center clickable",
    onClick: _cache[14] || (_cache[14] = $event => $setup.showArchiveExtraction = !$setup.showArchiveExtraction)
  }, [_cache[27] || (_cache[27] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: "files-tab-entry__avatar icon-play-white"
  }, null, -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_20, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Extract Archive')), 1 /* TEXT */)]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActions"], null, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionButton"], {
      modelValue: $setup.showArchiveExtraction,
      "onUpdate:modelValue": _cache[12] || (_cache[12] = $event => $setup.showArchiveExtraction = $event),
      icon: 'icon-triangle-' + ($setup.showArchiveExtraction ? 'n' : 's'),
      onClick: _cache[13] || (_cache[13] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(() => {}, ["stop"]))
    }, null, 8 /* PROPS */, ["modelValue", "icon"])]),
    _: 1 /* STABLE */
  })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", _hoisted_21, [$setup.loading ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_22)) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_23, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["FilePrefixPicker"], {
    modelValue: $setup.archiveExtractFileInfo,
    "onUpdate:modelValue": _cache[15] || (_cache[15] = $event => $setup.archiveExtractFileInfo = $event),
    hint: $setup.t($setup.appName, 'Choose a directory to extract the archive to:'),
    placeholder: $setup.t($setup.appName, 'basename'),
    onSubmit: $setup.extractArchive
  }, null, 8 /* PROPS */, ["modelValue", "hint", "placeholder"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_24, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: "label",
    onClick: $setup.openExtractionOptionsMenu
  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Extraction Options')), 1 /* TEXT */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActions"], {
    ref: "extractionOptionsComponent",
    forceMenu: true
  }, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionCheckbox"], {
      modelValue: $setup.archiveExtractStripCommonPathPrefix,
      "onUpdate:modelValue": _cache[16] || (_cache[16] = $event => $setup.archiveExtractStripCommonPathPrefix = $event),
      onChange: _cache[17] || (_cache[17] = $event => $setup.archiveExtractStripCommonPathPrefix = !$setup.archiveExtractStripCommonPathPrefix)
    }, {
      default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'strip common path prefix')), 1 /* TEXT */)]),
      _: 1 /* STABLE */
    }, 8 /* PROPS */, ["modelValue"]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionCheckbox"], {
      modelValue: $setup.archiveExtractBackgroundJob,
      "onUpdate:modelValue": _cache[18] || (_cache[18] = $event => $setup.archiveExtractBackgroundJob = $event),
      onChange: _cache[19] || (_cache[19] = $event => $setup.archiveExtractBackgroundJob = !$setup.archiveExtractBackgroundJob)
    }, {
      default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'schedule as background job')), 1 /* TEXT */)]),
      _: 1 /* STABLE */
    }, 8 /* PROPS */, ["modelValue"])]),
    _: 1 /* STABLE */
  }, 512 /* NEED_PATCH */)])]))], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vShow, $setup.showArchiveExtraction]]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", {
    class: "files-tab-entry flex flex-center clickable",
    onClick: _cache[22] || (_cache[22] = $event => $setup.showPendingJobs = !$setup.showPendingJobs)
  }, [_cache[28] || (_cache[28] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
    class: "files-tab-entry__avatar icon-recent-white"
  }, null, -1 /* CACHED */)), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_25, [$setup.jobsArePending ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("h5", _hoisted_26, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_27, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Pending Background Jobs')), 1 /* TEXT */), $setup.jobsArePending ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_28, "(" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)('' + Object.keys($setup.pendingJobs).length) + ")", 1 /* TEXT */)) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)])) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("h5", _hoisted_29, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_30, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'No Pending Background Jobs')), 1 /* TEXT */)]))]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActions"], null, {
    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionButton"], {
      modelValue: $setup.showPendingJobs,
      "onUpdate:modelValue": _cache[20] || (_cache[20] = $event => $setup.showPendingJobs = $event),
      icon: 'icon-triangle-' + ($setup.showPendingJobs ? 'n' : 's'),
      onClick: _cache[21] || (_cache[21] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)(() => {}, ["stop"]))
    }, null, 8 /* PROPS */, ["modelValue", "icon"])]),
    _: 1 /* STABLE */
  })]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("li", _hoisted_31, [$setup.loading ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_32)) : $setup.jobsArePending ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("ul", _hoisted_33, [((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.pendingJobs, job => {
    return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup["NcListItem"], {
      key: job.destinationPath,
      forceDisplayActions: true,
      bold: false
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createSlots)({
      title: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(job.destinationPath), 1 /* TEXT */)]),
      actions: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NcActionButton"], {
        onClick: $event => $setup.cancelPendingOperation(job.target)
      }, {
        icon: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["CancelIcon"], {
          size: 20
        }, null, 512 /* NEED_PATCH */), [[$setup["vTooltip"], $setup.t($setup.appName, 'Cancel Job')]])]),
        _: 1 /* STABLE */
      }, 8 /* PROPS */, ["onClick"])]),
      _: 2 /* DYNAMIC */
    }, [job.stripCommonPathPrefix ? {
      name: "extra",
      fn: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Job type: {type}', {
        type: job.target === 'mount' ? $setup.t($setup.appName, 'mount') : $setup.t($setup.appName, 'extract')
      })), 1 /* TEXT */), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'Common prefix {prefix} will be stripped.', {
        prefix: $setup.commonPathPrefix
      })), 1 /* TEXT */)]),
      key: "0"
    } : undefined]), 1024 /* DYNAMIC_SLOTS */);
  }), 128 /* KEYED_FRAGMENT */))])) : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_34, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.t($setup.appName, 'No pending background job.')), 1 /* TEXT */))], 512 /* NEED_PATCH */), [[vue__WEBPACK_IMPORTED_MODULE_0__.vShow, $setup.jobsArePending && $setup.showPendingJobs]])])]);
}

/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/config.ts"
/*!*********************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/config.ts ***!
  \*********************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   appName: () => (/* binding */ appName)
/* harmony export */ });
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
const appName = "files_archive";

/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/directives/Tooltip/index.ts"
/*!***************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/directives/Tooltip/index.ts ***!
  \***************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ floating_vue__WEBPACK_IMPORTED_MODULE_0__.vTooltip),
/* harmony export */   options: () => (/* reexport safe */ floating_vue__WEBPACK_IMPORTED_MODULE_0__.options)
/* harmony export */ });
/* harmony import */ var floating_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! floating-vue */ "./node_modules/floating-vue/dist/floating-vue.mjs");
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.scss */ "./repositories/rotdrop/nextcloud-vue-components/lib/directives/Tooltip/index.scss");
/**
 * SPDX-FileCopyrightText: 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

// @ts-expect-error 2882 I do not care.

floating_vue__WEBPACK_IMPORTED_MODULE_0__.options.themes.tooltip.html = false;
floating_vue__WEBPACK_IMPORTED_MODULE_0__.options.themes.tooltip.delay = {
  show: 500,
  hide: 200
};
floating_vue__WEBPACK_IMPORTED_MODULE_0__.options.themes.tooltip.distance = 10;
floating_vue__WEBPACK_IMPORTED_MODULE_0__.options.themes.tooltip['arrow-padding'] = 3;


/***/ },

/***/ "./src/console.ts"
/*!************************!*\
  !*** ./src/console.ts ***!
  \************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _toolkit_util_console_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./toolkit/util/console.ts */ "./src/toolkit/util/console.ts");
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

const logger = new _toolkit_util_console_ts__WEBPACK_IMPORTED_MODULE_0__["default"]('FilesArchive');
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (logger);

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

/***/ "./src/toolkit/util/console.ts"
/*!*************************************!*\
  !*** ./src/toolkit/util/console.ts ***!
  \*************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   stackTraceOptions: () => (/* binding */ stackTraceOptions)
/* harmony export */ });
/* harmony import */ var stacktrace_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! stacktrace-js */ "./node_modules/stacktrace-js/stacktrace.js");
/* harmony import */ var stacktrace_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(stacktrace_js__WEBPACK_IMPORTED_MODULE_0__);
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == typeof i ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != typeof t || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != typeof i) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
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

const stackTraceOptions = {
  sourceMapConsumerCache: {},
  sourceCache: {}
};
const syncStackFrames = (offset, depth) => stacktrace_js__WEBPACK_IMPORTED_MODULE_0___default().getSync(stackTraceOptions).slice(offset + 1, offset + 1 + depth);
const asyncStackFrames = async (offset, depth) => {
  const stackFrames = await stacktrace_js__WEBPACK_IMPORTED_MODULE_0___default().get(stackTraceOptions);
  return stackFrames.slice(offset + 1, offset + 1 + depth);
};
const defaultConsoleOptions = {
  smaps: {
    debug: true,
    info: true,
    error: true,
    trace: true
  },
  stackDepth: 0
};
class Console {
  constructor(prefix, options) {
    _defineProperty(this, "prefix", void 0);
    _defineProperty(this, "smaps", void 0);
    _defineProperty(this, "stackDepth", void 0);
    this.prefix = prefix;
    options = {
      ...defaultConsoleOptions,
      ...(options || {})
    };
    this.smaps = {
      ...{
        debug: true,
        info: true,
        error: true,
        trace: true
      },
      ...(options?.smaps || {})
    };
    this.stackDepth = options?.stackDepth || 0;
  }
  timestamp() {
    return new Date().toLocaleTimeString('en-gb', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      fractionalSecondDigits: 3
    });
  }
  async asyncStackFrames(depth) {
    try {
      return await asyncStackFrames(4, depth);
    } catch {
      return [];
    }
  }
  syncStackFrames(depth) {
    try {
      return syncStackFrames(4, depth);
    } catch {
      return [];
    }
  }
  locationObject(stack, time) {
    const prefix = time + ' ' + this.prefix + (stack.length > 0 ? ' ' + stack[0].toString() : '');
    return stack.length > 1 ? [prefix, {
      stack: stack.map(entry => entry.toString())
    }] : [prefix];
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  emitMessage(method, ...args) {
    const time = this.timestamp();
    const depth = Math.max(1, args.length > 0 && typeof args[0] === 'number' ? args.shift() : this.stackDepth);
    if (this.smaps[method]) {
      this.asyncStackFrames(depth).then(stack => {
        console[method](...this.locationObject(stack, time), ...args);
      });
    } else {
      console[method](...this.locationObject(this.syncStackFrames(depth), time), ...args);
    }
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  debug(...args) {
    return this.emitMessage('debug', ...args);
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  info(...args) {
    return this.emitMessage('info', ...args);
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  error(...args) {
    return this.emitMessage('error', ...args);
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  trace(...args) {
    return this.emitMessage('trace', ...args);
  }
  enableSourceMaps(method, state = true) {
    this.smaps[method] = state;
  }
  disableSourceMaps(method) {
    this.enableSourceMaps(method, false);
  }
  withStack(depth) {
    this.stackDepth = depth;
  }
  withoutStack() {
    this.stackDepth = 0;
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Console);

/***/ },

/***/ "./src/toolkit/util/file-node-busy-indicator.ts"
/*!******************************************************!*\
  !*** ./src/toolkit/util/file-node-busy-indicator.ts ***!
  \******************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clearFileNodeBusy: () => (/* binding */ clearFileNodeBusy),
/* harmony export */   setFileNodeBusy: () => (/* binding */ setFileNodeBusy)
/* harmony export */ });
/* harmony import */ var _nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @nextcloud/event-bus */ "./node_modules/@nextcloud/event-bus/dist/index.mjs");
/* harmony import */ var _nextcloud_files__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @nextcloud/files */ "./node_modules/@nextcloud/files/dist/index.mjs");
/**
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


const busyNodes = [];
const setFileNodeBusy = (node, state = true) => {
  if (node && state) {
    node.status = _nextcloud_files__WEBPACK_IMPORTED_MODULE_1__.NodeStatus.LOADING;
    (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_0__.emit)('files:node:updated', node);
    busyNodes.push(node);
  } else {
    for (const node of busyNodes) {
      node.status = undefined;
      (0,_nextcloud_event_bus__WEBPACK_IMPORTED_MODULE_0__.emit)('files:node:updated', node);
    }
    busyNodes.splice(0, busyNodes.length);
  }
};
const clearFileNodeBusy = () => setFileNodeBusy(undefined, false);

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
/* harmony import */ var _nextcloud_files__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @nextcloud/files */ "./node_modules/@nextcloud/files/dist/index.mjs");
/* harmony import */ var _nextcloud_router__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @nextcloud/router */ "./node_modules/@nextcloud/router/dist/index.mjs");
/* harmony import */ var path__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! path */ "./node_modules/path-browserify/index.js");
/* harmony import */ var path__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(path__WEBPACK_IMPORTED_MODULE_3__);
/**
 * @copyright Copyright (c) 2024, 2025, 2025, 2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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
    throw new Error(`${fileInfo.path} is located outside of the front end user file space ${userFrontEndFolder}.`);
  }
  const nodeData = {
    id: parseInt(fileInfo.fileid, 10),
    source: (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_2__.generateRemoteUrl)((0,path__WEBPACK_IMPORTED_MODULE_3__.join)('dav/files', owner, fileInfo.relativePath)),
    root: `/files/${owner}`,
    mime: fileInfo.mime,
    mtime: new Date(fileInfo.lastmod * 1000),
    owner,
    size: fileInfo.size,
    permissions: fileInfo.permissions,
    attributes: {
      ...fileInfo,
      'has-preview': fileInfo.hasPreview
    }
  };
  return fileInfo.type === 'file' ? new _nextcloud_files__WEBPACK_IMPORTED_MODULE_1__.File(nodeData) : new _nextcloud_files__WEBPACK_IMPORTED_MODULE_1__.Folder(nodeData);
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
/* harmony import */ var _nextcloud_router__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @nextcloud/router */ "./node_modules/@nextcloud/router/dist/index.mjs");
/* harmony import */ var _config_ts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config.ts */ "./src/config.ts");
/**
 * @copyright Copyright (c) 2022-2026 Claus-Justus Heine <himself@claus-justus-heine.de>
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
  let generated = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_0__.generateUrl)('/apps/' + _config_ts__WEBPACK_IMPORTED_MODULE_1__.appName + '/' + url, urlParams, urlOptions);
  // remove missing parameters as optional
  generated = generated.replace(/\/%7B[^%]+%7D/g, '');
  const queryParams = {
    ...(urlParams || {})
  };
  for (const urlParam of url.matchAll(/{([^{}]*)}/g)) {
    delete queryParams[urlParam[1]];
  }
  const queryArray = [];
  for (const [key, value] of Object.entries(queryParams)) {
    try {
      queryArray.push(key + '=' + encodeURIComponent(value?.toString() || ''));
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
  let generated = (0,_nextcloud_router__WEBPACK_IMPORTED_MODULE_0__.generateOcsUrl)('/apps/' + _config_ts__WEBPACK_IMPORTED_MODULE_1__.appName + '/' + url, urlParams, urlOptions);
  // depending on the version of @nextcloud/router there are further duplicate slashes, oh well.
  generated = generated.replace(/\/\/ocs/g, '/ocs');
  const queryParams = {
    ...urlParams
  };
  for (const urlParam of url.matchAll(/{([^{}]*)}/g)) {
    delete queryParams[urlParam[1]];
  }
  const queryArray = [];
  for (const [key, value] of Object.entries(queryParams)) {
    try {
      queryArray.push(key + '=' + encodeURIComponent(value?.toString() || ''));
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

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/directives/Tooltip/index.scss"
/*!*****************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/directives/Tooltip/index.scss ***!
  \*****************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss"
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true"
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true"
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true"
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue"
/*!*******************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue ***!
  \*******************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _FilePrefixPicker_vue_vue_type_template_id_3568f856_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true");
/* harmony import */ var _FilePrefixPicker_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts");
/* harmony import */ var _FilePrefixPicker_vue_vue_type_style_index_0_id_3568f856_lang_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss");
/* harmony import */ var _FilePrefixPicker_vue_vue_type_style_index_1_id_3568f856_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;



const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_4__["default"])(_FilePrefixPicker_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_FilePrefixPicker_vue_vue_type_template_id_3568f856_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__.render],['__scopeId',"data-v-3568f856"],['__file',"repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue"
/*!****************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue ***!
  \****************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _TextFieldWithSubmitButton_vue_vue_type_template_id_40b67f33_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true");
/* harmony import */ var _TextFieldWithSubmitButton_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts");
/* harmony import */ var _TextFieldWithSubmitButton_vue_vue_type_style_index_0_id_40b67f33_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true */ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_TextFieldWithSubmitButton_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_TextFieldWithSubmitButton_vue_vue_type_template_id_40b67f33_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__.render],['__scopeId',"data-v-40b67f33"],['__file',"repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

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
/* harmony import */ var _FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true */ "./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true");
/* harmony import */ var _FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FilesTab.vue?vue&type=script&setup=true&lang=ts */ "./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts");
/* harmony import */ var _FilesTab_vue_vue_type_style_index_0_id_d56e0e50_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true */ "./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true");
/* harmony import */ var _node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__.render],['__scopeId',"data-v-d56e0e50"],['__file',"src/views/FilesTab.vue"]])
/* hot reload */
if (false) // removed by dead control flow
{}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts"
/*!******************************************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts ***!
  \******************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilePrefixPicker_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilePrefixPicker_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js!../../../../../node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts */ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=script&setup=true&lang=ts");
 

/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts"
/*!***************************************************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts ***!
  \***************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_TextFieldWithSubmitButton_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_TextFieldWithSubmitButton_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js!../../../../../node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts */ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=script&setup=true&lang=ts");
 

/***/ },

/***/ "./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts"
/*!*******************************************************************!*\
  !*** ./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts ***!
  \*******************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilesTab_vue_vue_type_script_setup_true_lang_ts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js!../../node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./FilesTab.vue?vue&type=script&setup=true&lang=ts */ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=script&setup=true&lang=ts");
 

/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true"
/*!*********************************************************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true ***!
  \*********************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilePrefixPicker_vue_vue_type_template_id_3568f856_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilePrefixPicker_vue_vue_type_template_id_3568f856_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js!../../../../../node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true */ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=template&id=3568f856&scoped=true&ts=true");


/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true"
/*!******************************************************************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true ***!
  \******************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_TextFieldWithSubmitButton_vue_vue_type_template_id_40b67f33_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_TextFieldWithSubmitButton_vue_vue_type_template_id_40b67f33_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js!../../../../../node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true */ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=template&id=40b67f33&scoped=true&ts=true");


/***/ },

/***/ "./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true"
/*!**********************************************************************************!*\
  !*** ./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true ***!
  \**********************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   render: () => (/* reexport safe */ _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_node_modules_ts_loader_index_js_clonedRuleSet_6_use_1_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilesTab_vue_vue_type_template_id_d56e0e50_scoped_true_ts_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js!../../node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true */ "./node_modules/babel-loader/lib/index.js!./node_modules/ts-loader/index.js??clonedRuleSet-6.use[1]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=template&id=d56e0e50&scoped=true&ts=true");


/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss"
/*!****************************************************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss ***!
  \****************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_3_use_2_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilePrefixPicker_vue_vue_type_style_index_0_id_3568f856_lang_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=0&id=3568f856&lang=scss");


/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true"
/*!****************************************************************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true ***!
  \****************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_3_use_2_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilePrefixPicker_vue_vue_type_style_index_1_id_3568f856_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/FilePrefixPicker.vue?vue&type=style&index=1&id=3568f856&lang=scss&scoped=true");


/***/ },

/***/ "./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true"
/*!*************************************************************************************************************************************************************!*\
  !*** ./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true ***!
  \*************************************************************************************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_3_use_2_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_TextFieldWithSubmitButton_vue_vue_type_style_index_0_id_40b67f33_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./repositories/rotdrop/nextcloud-vue-components/lib/components/TextFieldWithSubmitButton.vue?vue&type=style&index=0&id=40b67f33&lang=scss&scoped=true");


/***/ },

/***/ "./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true"
/*!*****************************************************************************************!*\
  !*** ./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true ***!
  \*****************************************************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_sass_loader_dist_cjs_js_clonedRuleSet_3_use_2_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_18_use_0_FilesTab_vue_vue_type_style_index_0_id_d56e0e50_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/mini-css-extract-plugin/dist/loader.js!../../node_modules/css-loader/dist/cjs.js!../../node_modules/vue-loader/dist/stylePostLoader.js!../../node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js??clonedRuleSet-3.use[2]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[18].use[0]!./src/views/FilesTab.vue?vue&type=style&index=0&id=d56e0e50&lang=scss&scoped=true");


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
//# sourceMappingURL=src_views_FilesTab_vue-c5f807b95d5b94ea5421.js.map