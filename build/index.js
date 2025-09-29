/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/appp-logo.png":
/*!******************************!*\
  !*** ./assets/appp-logo.png ***!
  \******************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

module.exports = __webpack_require__.p + "images/appp-logo.4efe5030.png";

/***/ }),

/***/ "./src/css/settings.css":
/*!******************************!*\
  !*** ./src/css/settings.css ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/publicPath */
/******/ 	(() => {
/******/ 		var scriptUrl;
/******/ 		if (__webpack_require__.g.importScripts) scriptUrl = __webpack_require__.g.location + "";
/******/ 		var document = __webpack_require__.g.document;
/******/ 		if (!scriptUrl && document) {
/******/ 			if (document.currentScript && document.currentScript.tagName.toUpperCase() === 'SCRIPT')
/******/ 				scriptUrl = document.currentScript.src;
/******/ 			if (!scriptUrl) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				if(scripts.length) {
/******/ 					var i = scripts.length - 1;
/******/ 					while (i > -1 && (!scriptUrl || !/^http(s?):/.test(scriptUrl))) scriptUrl = scripts[i--].src;
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 		// When supporting browsers where an automatic publicPath is not supported you must specify an output.publicPath manually via configuration
/******/ 		// or pass an empty string ("") and set the __webpack_public_path__ variable from your code to use your own logic.
/******/ 		if (!scriptUrl) throw new Error("Automatic publicPath is not supported in this browser");
/******/ 		scriptUrl = scriptUrl.replace(/^blob:/, "").replace(/#.*$/, "").replace(/\?.*$/, "").replace(/\/[^\/]+$/, "/");
/******/ 		__webpack_require__.p = scriptUrl;
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _css_settings_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./css/settings.css */ "./src/css/settings.css");
/* harmony import */ var _assets_appp_logo_png__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../assets/appp-logo.png */ "./assets/appp-logo.png");

/* eslint-disable camelcase */
/**
 * WordPress dependencies
 */
const {
  __
} = wp.i18n;
const {
  BaseControl,
  Button,
  ExternalLink,
  PanelBody,
  PanelRow,
  Placeholder,
  Popover,
  Spinner,
  ToggleControl,
  TextControl,
  SelectControl,
  CheckboxControl,
  Notice
} = wp.components;
const {
  render,
  Component,
  Fragment
} = wp.element;

/**
 * Internal dependencies
 */


class App extends Component {
  constructor() {
    super(...arguments);
    this.changeOptions = this.changeOptions.bind(this);
    this.saveOptions = this.saveOptions.bind(this);
    this.sendTestMessage = this.sendTestMessage.bind(this);
    this.roles = [];
    this.state = {
      isAPILoaded: false,
      isAPISaving: false,
      onesignal_app_id: '',
      onesignal_rest_api_key: '',
      github_access_token: '',
      onesignal_testing: false,
      onesignal_access: [],
      post_types_auto_push: [],
      onesignal_segments: [],
      onesignal_message: '',
      roles: [],
      postTypes: [],
      segments: [],
      notice: null
    };
  }
  componentDidMount() {
    // Load options
    wp.apiRequest({
      path: '/appsignal/v1/options'
    }).then(options => {
      this.setState({
        onesignal_app_id: options.onesignal_app_id || '',
        onesignal_rest_api_key: options.onesignal_rest_api_key || '',
        github_access_token: options.github_access_token || '',
        onesignal_testing: options.onesignal_testing || false,
        onesignal_access: options.onesignal_access || [],
        post_types_auto_push: options.post_types_auto_push || [],
        onesignal_segments: options.onesignal_segments || []
      });
    });

    // Load roles
    wp.apiRequest({
      path: '/appsignal/v1/roles'
    }).then(roles => {
      this.setState({
        roles
      });
    });

    // Load post types
    wp.apiRequest({
      path: '/appsignal/v1/post-types'
    }).then(postTypes => {
      this.setState({
        postTypes
      });
    });

    // Load segments
    wp.apiRequest({
      path: '/appsignal/v1/segments'
    }).then(segments => {
      this.setState({
        segments,
        isAPILoaded: true
      });
    });
  }
  changeOptions(option, value) {
    this.setState({
      [option]: value
    });
  }
  saveOptions() {
    this.setState({
      isAPISaving: true
    });
    const options = {
      onesignal_app_id: this.state.onesignal_app_id,
      onesignal_rest_api_key: this.state.onesignal_rest_api_key,
      github_access_token: this.state.github_access_token,
      onesignal_testing: this.state.onesignal_testing,
      onesignal_access: this.state.onesignal_access,
      post_types_auto_push: this.state.post_types_auto_push,
      onesignal_segments: this.state.onesignal_segments
    };
    wp.apiRequest({
      path: '/appsignal/v1/options',
      method: 'POST',
      data: options
    }).then(() => {
      this.setState({
        isAPISaving: false,
        notice: {
          type: 'success',
          message: __('Settings saved successfully!')
        }
      });
      setTimeout(() => this.setState({
        notice: null
      }), 3000);
    }).catch(() => {
      this.setState({
        isAPISaving: false,
        notice: {
          type: 'error',
          message: __('Failed to save settings.')
        }
      });
    });
  }
  sendTestMessage() {
    if (!this.state.onesignal_message.trim()) {
      this.setState({
        notice: {
          type: 'error',
          message: __('Please enter a test message.')
        }
      });
      return;
    }
    wp.apiRequest({
      path: '/appsignal/v1/test-message',
      method: 'POST',
      data: {
        message: this.state.onesignal_message
      }
    }).then(response => {
      this.setState({
        notice: {
          type: 'success',
          message: response.message
        },
        onesignal_message: ''
      });
      setTimeout(() => this.setState({
        notice: null
      }), 3000);
    }).catch(error => {
      this.setState({
        notice: {
          type: 'error',
          message: error.message || __('Failed to send test message.')
        }
      });
    });
  }
  render() {
    return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-header"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-container"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-logo",
      style: {
        display: 'flex',
        alignItems: 'center'
      }
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appp-icon"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
      style: {
        width: '50px',
        marginRight: '10px',
        borderRadius: '4px'
      },
      src: _assets_appp_logo_png__WEBPACK_IMPORTED_MODULE_2__,
      alt: "AppPresser"
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h1", {
      style: {
        margin: '0px'
      }
    }, __('AppSignal')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
      style: {
        margin: '10px 0px 0px 0px'
      }
    }, __('By AppPresser')))))), !this.state.isAPILoaded ? (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-spinner-center"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Placeholder, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "d-flex justify-content-center"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Spinner, null)))) : (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-main"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelBody, {
      title: __('OneSignal Configuration'),
      initialOpen: true
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('OneSignal App ID'),
      value: this.state.onesignal_app_id,
      help: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, __('The App ID for OneSignal. '), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(ExternalLink, {
        href: "https://documentation.onesignal.com/docs/keys-and-ids",
        target: "_blank",
        rel: "noopener noreferrer"
      }, __('Learn more.'))),
      placeholder: __('OneSignal App ID'),
      onChange: value => this.changeOptions('onesignal_app_id', value)
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('OneSignal REST Key'),
      value: this.state.onesignal_rest_api_key,
      help: __('The OneSignal REST Secret Key. DO NOT expose this key to anyone.'),
      placeholder: __('OneSignal REST Key'),
      onChange: value => this.changeOptions('onesignal_rest_api_key', value)
    }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelBody, {
      title: __('Access Control')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BaseControl, {
      label: __('User Role Access'),
      help: __('Choose user roles that can access push notifications.')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-checkbox-group"
    }, this.state.roles.map(role => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      key: role.value,
      label: role.label,
      checked: this.state.onesignal_access.includes(role.value),
      onChange: checked => {
        const newAccess = checked ? [...this.state.onesignal_access, role.value] : this.state.onesignal_access.filter(r => r !== role.value);
        this.changeOptions('onesignal_access', newAccess);
      }
    })))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelBody, {
      title: __('Post Types')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BaseControl, {
      label: __('Post Push'),
      help: __('Choose post types to add push metabox.')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-checkbox-group"
    }, this.state.postTypes.map(postType => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      key: postType.value,
      label: postType.label,
      checked: this.state.post_types_auto_push.includes(postType.value),
      onChange: checked => {
        const newTypes = checked ? [...this.state.post_types_auto_push, postType.value] : this.state.post_types_auto_push.filter(t => t !== postType.value);
        this.changeOptions('post_types_auto_push', newTypes);
      }
    })))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelBody, {
      title: __('Segments & Testing')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(ToggleControl, {
      label: __('Testing Mode'),
      help: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, __('Send notifications to testing segment. '), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(ExternalLink, {
        href: "https://documentation.onesignal.com/docs/segmentation",
        target: "_blank",
        rel: "noopener noreferrer"
      }, __('Learn more.'))),
      checked: this.state.onesignal_testing,
      onChange: checked => this.changeOptions('onesignal_testing', checked)
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(BaseControl, {
      label: __('Segments'),
      help: __('Select the segments to send notifications to.')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-checkbox-group"
    }, this.state.segments.map(segment => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      key: segment.value,
      label: segment.label,
      checked: this.state.onesignal_segments.includes(segment.value),
      onChange: checked => {
        const newSegments = checked ? [...this.state.onesignal_segments, segment.value] : this.state.onesignal_segments.filter(s => s !== segment.value);
        this.changeOptions('onesignal_segments', newSegments);
      }
    })))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelBody, {
      title: __('Test Message')
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Test Message'),
      value: this.state.onesignal_message,
      help: __('Send a test message to selected segments.'),
      placeholder: __('Enter test message...'),
      onChange: value => this.changeOptions('onesignal_message', value)
    })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-text-field-button-group flex-right"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Button, {
      isSecondary: true,
      disabled: !this.state.onesignal_message.trim(),
      onClick: () => this.sendTestMessage()
    }, __('Send Test Message'))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-text-field-button-group flex-right"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Button, {
      isPrimary: true,
      isLarge: true,
      disabled: this.state.isAPISaving,
      onClick: () => this.saveOptions(),
      className: "save-button"
    }, this.state.isAPISaving ? __('Saving...') : __('Save Settings'))), this.state.notice && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "appsignal-notice"
    }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Notice, {
      status: this.state.notice.type,
      isDismissible: true,
      onRemove: () => this.setState({
        notice: null
      })
    }, this.state.notice.message))));
  }
}
render((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(App, null), document.getElementById('appsignal'));
})();

/******/ })()
;
//# sourceMappingURL=index.js.map