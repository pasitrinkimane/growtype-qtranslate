(()=>{"use strict";function t(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function e(e){for(var n=1;n<arguments.length;n++){var a=null!=arguments[n]?arguments[n]:{};n%2?t(Object(a),!0).forEach((function(t){r(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):t(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}function r(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}wp.apiFetch.use((function(t,r){if(t.path.indexOf("/v2/blocks")>-1){var n=sessionStorage.getItem("qtranslate-xt-admin-edit-language");if(n.length){var a="&qtx_editor_lang="+n;"PUT"!==t.method&&"POST"!==t.method?t.path=t.path+a:"PUT"===t.method&&(t.data.qtx_editor_lang=n)}}if(!t.path||"PUT"!==t.method&&"POST"!==t.method)return r(t);var o=wp.data.select("core/editor");if(!o)return r(t);var i=o.getCurrentPost();if(!i.hasOwnProperty("type"))return r(t);var s=wp.data.select("core").getPostType(i.type);if(!s.hasOwnProperty("rest_base"))return r(t);var c="/wp/v2/"+s.rest_base+"/"+i.id;return t.path.startsWith(c)&&"PUT"===t.method||t.path.startsWith(c+"/autosaves")&&"POST"===t.method?i.hasOwnProperty("qtx_editor_lang")?r(e(e({},t),{},{data:e(e({},t.data),{},{qtx_editor_lang:i.qtx_editor_lang})})):(console.log("QT-XT API: missing field [qtx_editor_lang] in post id="+i.id),r(t)):r(t)}))})();