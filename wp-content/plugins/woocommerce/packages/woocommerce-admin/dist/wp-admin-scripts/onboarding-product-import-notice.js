<<<<<<< HEAD
this.wc=this.wc||{},this.wc.onboardingProductImportNotice=function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=464)}({2:function(t,e){!function(){t.exports=this.wp.i18n}()},25:function(t,e,n){"use strict";n.d(e,"a",(function(){return s})),n.d(e,"b",(function(){return d})),n.d(e,"c",(function(){return f})),n.d(e,"d",(function(){return l})),n.d(e,"e",(function(){return p})),n.d(e,"g",(function(){return b})),n.d(e,"h",(function(){return y})),n.d(e,"f",(function(){return m}));var r=n(30),o=n.n(r),u=n(2),i=["wcAdminSettings","preloadSettings"],c="object"===("undefined"==typeof wcSettings?"undefined":o()(wcSettings))?wcSettings:{},a=Object.keys(c).reduce((function(t,e){return i.includes(e)||(t[e]=c[e]),t}),{}),s=a.adminUrl,d=(a.countries,a.currency),f=a.locale,l=a.orderStatuses,p=(a.siteTitle,a.wcAssetUrl);function b(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1],n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:function(t){return t};if(i.includes(t))throw new Error(Object(u.__)("Mutable settings should be accessed via data store."));var r=a.hasOwnProperty(t)?a[t]:e;return n(r,e)}function y(t,e){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:function(t){return t};if(i.includes(t))throw new Error(Object(u.__)("Mutable settings should be mutated via data store."));a[t]=n(e)}function m(t){return(s||"")+t}},30:function(t,e){function n(e){return"function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?t.exports=n=function(t){return typeof t}:t.exports=n=function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},n(e)}t.exports=n},464:function(t,e,n){"use strict";n.r(e);var r=n(2),o=n(54),u=n(25);Object(o.a)((function(){var t=document.querySelector(".wc-actions");if(t){var e=document.querySelector(".wc-actions .button-primary");e&&(e.classList.remove("button"),e.classList.remove("button-primary"));var n=document.createElement("a");n.classList.add("button"),n.classList.add("button-primary"),n.setAttribute("href",Object(u.f)("admin.php?page=wc-admin")),n.innerText=Object(r.__)("Continue setup",'woocommerce'),t.appendChild(n)}}))},54:function(t,e,n){"use strict";function r(t){"complete"!==document.readyState&&"interactive"!==document.readyState?document.addEventListener("DOMContentLoaded",t):t()}n.d(e,"a",(function(){return r}))}});
=======
this.wc=this.wc||{},this.wc.onboardingProductImportNotice=function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=471)}({2:function(t,e){!function(){t.exports=this.wp.i18n}()},25:function(t,e,n){"use strict";n.d(e,"a",(function(){return s})),n.d(e,"b",(function(){return d})),n.d(e,"c",(function(){return f})),n.d(e,"d",(function(){return l})),n.d(e,"e",(function(){return p})),n.d(e,"g",(function(){return b})),n.d(e,"h",(function(){return y})),n.d(e,"f",(function(){return m}));var r=n(30),o=n.n(r),u=n(2),i=["wcAdminSettings","preloadSettings"],c="object"===("undefined"==typeof wcSettings?"undefined":o()(wcSettings))?wcSettings:{},a=Object.keys(c).reduce((function(t,e){return i.includes(e)||(t[e]=c[e]),t}),{}),s=a.adminUrl,d=(a.countries,a.currency),f=a.locale,l=a.orderStatuses,p=(a.siteTitle,a.wcAssetUrl);function b(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1],n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:function(t){return t};if(i.includes(t))throw new Error(Object(u.__)("Mutable settings should be accessed via data store."));var r=a.hasOwnProperty(t)?a[t]:e;return n(r,e)}function y(t,e){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:function(t){return t};if(i.includes(t))throw new Error(Object(u.__)("Mutable settings should be mutated via data store."));a[t]=n(e)}function m(t){return(s||"")+t}},30:function(t,e){function n(e){return"function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?t.exports=n=function(t){return typeof t}:t.exports=n=function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},n(e)}t.exports=n},471:function(t,e,n){"use strict";n.r(e);var r=n(2),o=n(54),u=n(25);Object(o.a)((function(){var t=document.querySelector(".wc-actions");if(t){var e=document.querySelector(".wc-actions .button-primary");e&&(e.classList.remove("button"),e.classList.remove("button-primary"));var n=document.createElement("a");n.classList.add("button"),n.classList.add("button-primary"),n.setAttribute("href",Object(u.f)("admin.php?page=wc-admin")),n.innerText=Object(r.__)("Continue setup",'woocommerce'),t.appendChild(n)}}))},54:function(t,e,n){"use strict";function r(t){"complete"!==document.readyState&&"interactive"!==document.readyState?document.addEventListener("DOMContentLoaded",t):t()}n.d(e,"a",(function(){return r}))}});
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
