(()=>{"use strict";function t(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(t);e&&(a=a.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,a)}return r}function e(e){for(var a=1;a<arguments.length;a++){var n=null!=arguments[a]?arguments[a]:{};a%2?t(Object(n),!0).forEach((function(t){r(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):t(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function r(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}wp.apiFetch.use((function(t,r){if(t.path&&t.path.indexOf("/v2/blocks")>-1){var a=sessionStorage.getItem("qtranslate-xt-admin-edit-language");if(a.length){var n="&qtx_editor_lang="+a;"PUT"!==t.method&&"POST"!==t.method&&"OPTIONS"!==t.method?-1===t.path.indexOf(n)&&(t.path=t.path+n):"PUT"===t.method&&(t.data.qtx_editor_lang=a)}}if(!t.path||"PUT"!==t.method&&"POST"!==t.method)return r(t);var o=wp.data.select("core/editor");if(!o)return r(t);var i=o.getCurrentPost();if(!i.hasOwnProperty("type"))return r(t);var s=wp.data.select("core").getPostType(i.type);if(!s.hasOwnProperty("rest_base"))return r(t);var p="/wp/v2/"+s.rest_base+"/"+i.id;return t.path.startsWith(p)&&"PUT"===t.method||t.path.startsWith(p+"/autosaves")&&"POST"===t.method?i.hasOwnProperty("qtx_editor_lang")?r(e(e({},t),{},{data:e(e({},t.data),{},{qtx_editor_lang:i.qtx_editor_lang})})):(console.log("QT-XT API: missing field [qtx_editor_lang] in post id="+i.id),r(t)):r(t)}))})();