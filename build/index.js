/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/sidebar.js":
/*!***************************!*\
  !*** ./src/js/sidebar.js ***!
  \***************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);









const AppsignalDocumentSettingPanel = ({
  meta,
  setMeta
}) => {
  const postStatus = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useSelect)(select => {
    return select('core/editor').getCurrentPost().status;
  }, []);
  const [isSending, setIsSending] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useState)(false);
  const [sendStatus, setSendStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useState)(null);
  const [titleError, setTitleError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useState)(null);
  const [messageError, setMessageError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useState)(null);
  const [localToggleValue, setLocalToggleValue] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useState)(null);
  const {
    createNotice
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useDispatch)('core/notices');
  const {
    savePost
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useDispatch)('core/editor');

  // Track previous status to detect transition to 'publish'
  const prevStatus = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useRef)(postStatus);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useEffect)(() => {
    if (prevStatus.current !== 'publish' && postStatus === 'publish') {
      // Post has just been published!
      // Place your logic here (e.g., show a notice, call an API, etc.)
      console.log('Post was published!');
      sendNotification();
    }
    prevStatus.current = postStatus;
  }, [postStatus]);
  if (!meta) {
    return null;
  }

  /**
   * Send push notification via API
   */
  const sendNotification = async () => {
    const title = meta.appsignal_notification_title || '';
    const message = meta.appsignal_notification_message || '';
    let hasError = false;
    if (!title.trim()) {
      setTitleError((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Title is required.', 'apppresser-onesignal'));
      hasError = true;
    } else {
      setTitleError(null);
    }
    if (!message.trim()) {
      setMessageError((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Message is required.', 'apppresser-onesignal'));
      hasError = true;
    } else {
      setMessageError(null);
    }
    if (hasError) {
      return;
    }
    setIsSending(true);
    setSendStatus(null);
    try {
      await savePost();
      const response = await window.fetch(`${window.appsignalOneSignalData?.rest_url}appsignal/v1/send`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.appsignalOneSignalData?.nonce || ''
        },
        body: JSON.stringify({
          post_id: window.appsignalOneSignalData?.post_id || 0
        })
      });
      if (!response.ok) {
        throw new Error('Failed to send notification');
      }
      setSendStatus('success');
      createNotice('success', (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Push notification sent successfully!', 'apppresser-onesignal'), {
        id: 'appsignal-notice',
        isDismissible: true
      });
    } catch (error) {
      console.error('Error sending push notification:', error);
      setSendStatus('error');
      createNotice('error', (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to send push notification. Please try again.', 'apppresser-onesignal'), {
        id: 'appsignal-notice',
        isDismissible: true
      });
    } finally {
      setIsSending(false);
    }
  };

  /**
  * Get help text based on post status
  */
  const getHelpText = () => {
    return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('A notification will be sent when this post is published.', 'apppresser-onesignal');
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__.PluginDocumentSettingPanel, {
    name: "appsignal-document-setting-panel",
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Push Notification', 'apppresser-onesignal')
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Send push notification', 'apppresser-onesignal'),
    checked: meta.appsignal_send_notification,
    onChange: value => {
      setMeta({
        ...meta,
        appsignal_send_notification: value
      });
    },
    disabled: !meta.appsignal_notification_title || !meta.appsignal_notification_message || postStatus === 'publish',
    help: getHelpText()
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Title', 'apppresser-onesignal'),
    value: meta.appsignal_notification_title || '',
    onChange: value => {
      setMeta({
        ...meta,
        appsignal_notification_title: value
      });
      if (value.trim()) {
        setTitleError(null);
      }
    },
    maxLength: 30,
    help: `${(meta.appsignal_notification_title || '').length}/30`
  }), titleError && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    style: {
      color: '#d63638',
      marginTop: '-10px',
      fontSize: '12px',
      marginBottom: '10px'
    }
  }, titleError), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextareaControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Message', 'apppresser-onesignal'),
    value: meta.appsignal_notification_message || '',
    onChange: value => {
      setMeta({
        ...meta,
        appsignal_notification_message: value
      });
      if (value.trim()) {
        setMessageError(null);
      }
    },
    maxLength: 60,
    help: `${(meta.appsignal_notification_message || '').length}/60`
  }), messageError && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    style: {
      color: '#d63638',
      marginTop: '-10px',
      fontSize: '12px',
      marginBottom: '10px'
    }
  }, messageError), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    style: {
      width: '100%'
    }
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, {
    variant: "secondary",
    onClick: sendNotification,
    isBusy: isSending,
    disabled: isSending,
    style: {
      marginTop: '16px',
      width: '100%',
      textAlign: 'center',
      display: 'flex',
      justifyContent: 'center'
    }
  }, isSending ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sending...', 'apppresser-onesignal') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Send Push', 'apppresser-onesignal')))));
};
const ComposedPanel = (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_6__.compose)([(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.withSelect)(select => {
  const meta = select('core/editor').getEditedPostAttribute('meta');
  return {
    meta: meta
  };
}), (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.withDispatch)(dispatch => {
  return {
    setMeta(newMeta) {
      dispatch('core/editor').editPost({
        meta: newMeta
      });
    }
  };
})])(AppsignalDocumentSettingPanel);
(0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_2__.registerPlugin)('appsignal-document-setting-panel', {
  render: ComposedPanel
});

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["compose"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/editor":
/*!********************************!*\
  !*** external ["wp","editor"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["editor"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/plugins":
/*!*********************************!*\
  !*** external ["wp","plugins"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["plugins"];

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
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _js_sidebar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./js/sidebar */ "./src/js/sidebar.js");
/**
 * WordPress dependencies
 */

})();

/******/ })()
;
//# sourceMappingURL=index.js.map