!function(t){var e={};function i(n){if(e[n])return e[n].exports;var r=e[n]={i:n,l:!1,exports:{}};return t[n].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.m=t,i.c=e,i.d=function(t,e,n){i.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},i.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},i.t=function(t,e){if(1&e&&(t=i(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)i.d(n,r,function(e){return t[e]}.bind(null,r));return n},i.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return i.d(e,"a",e),e},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},i.p="",i(i.s=15)}({15:function(t,e,i){"use strict";i.r(e);var n=function(){function t(t,e){this.table=t,this.wrapper=e,this.initialX=0,this.xOffset=0,this.maxX=0,this.tempX=0,this.active=!1,this.create(),this.updateDraggerWidth(),this.updateWidth()}return t.prototype.disable=function(){this.element.style.display="none"},t.prototype.enable=function(){this.element.style.display="block"},t.prototype.hide=function(){this.element.classList.add("-hidden")},t.prototype.show=function(){this.element.classList.remove("-hidden")},t.prototype.create=function(){var t=this,e=document.createElement("div"),i=document.createElement("div");e.classList.add("acp-scrolling-indicator"),e.classList.add("-start"),e.classList.add("-hidden"),i.classList.add("acp-scrolling-indicator__dragger"),e.appendChild(i),document.body.appendChild(e),this.element=e,this.dragger=i,setTimeout((function(){t.maxX=e.clientWidth-i.offsetWidth})),this.initEvents(),this.disable(),this.updateYPosition()},t.prototype.updateWidth=function(){this.element.style.width=this.table.offsetWidth+"px"},t.prototype.updateYPosition=function(){var t=this.table.getBoundingClientRect().bottom,e=window.innerHeight-t;e>this.element.offsetHeight?this.element.style.top=window.innerHeight-e+"px":this.element.style.top="inherit"},t.prototype.updateDraggerWidth=function(){var t=Math.round(this.table.offsetWidth/this.table.scrollWidth*100);100===t?this.element.classList.add("-fits"):this.element.classList.remove("-fits"),this.dragger.style.width=t+"%",this.maxX=this.element.clientWidth-this.dragger.offsetWidth},t.prototype.initEvents=function(){var t=this;document.addEventListener("scroll",(function(){t.updateYPosition(),t.updateWidth()})),window.addEventListener("touchmove",(function(e){return t.drag(e)}),!1),window.addEventListener("mousemove",(function(e){return t.drag(e)}),!1),window.addEventListener("mouseup",(function(e){return t.dragEnd()})),this.dragger.addEventListener("touchstart",(function(e){return t.dragStart(e)})),this.dragger.addEventListener("touchend",(function(){return t.dragEnd()})),this.dragger.addEventListener("mousedown",(function(e){return t.dragStart(e)})),this.dragger.addEventListener("mouseup",(function(){return t.dragEnd()})),this.table.addEventListener("scroll",(function(){t.updateIndicator()})),window.addEventListener("resize",(function(){clearTimeout(t.timeout),t.timeout=setTimeout((function(){t.updateWidth(),t.updateDraggerWidth()}),100)})),r(document.getElementById("wpbody-content"),(function(){t.refreshPosition(300)}))},t.prototype.refreshPosition=function(t){var e=this;void 0===t&&(t=100),setTimeout((function(){e.show(),e.updateYPosition()}),t)},t.prototype.updateIndicator=function(){if(!this.active){var t=this.wrapper.getOffsetPercentage()/100,e=Math.round(this.maxX*t);e>this.maxX&&(e=this.maxX),this.xOffset=e,this.setTranslate(e)}},t.prototype.getCurrentOffset=function(){return this.xOffset},t.prototype.dragStart=function(t){this.initialX=o.getX(t),this.active=!0},t.prototype.dragEnd=function(){this.active&&(this.initialX=0,this.active=!1,this.xOffset=this.tempX,AdminColumns.HorizontalScrolling.wrapper.setOffset(this.percentage))},t.prototype.drag=function(t){if(this.active){t.preventDefault();var e=o.getX(t)-this.initialX,i=this.getCurrentOffset()+e;i<0&&(i=0),i>this.maxX&&(i=this.maxX),this.tempX=i,this.percentage=i/this.maxX*100,this.setTranslate(i),AdminColumns.HorizontalScrolling.wrapper.setOffset(this.percentage)}},t.prototype.setTranslate=function(t){0===t?this.element.classList.add("-start"):this.element.classList.remove("-start"),this.dragger.style.left=t+"px"},t}(),r=function(t,e){var i,n=t.clientHeight;!function r(){i=t.clientHeight,n!==i&&e(i),n=i,parseInt(t.dataset.onElementHeightChangeTimer)&&clearTimeout(parseInt(t.dataset.onElementHeightChangeTimer)),t.dataset.onElementHeightChangeTimer=setTimeout(r,200)}()},o=function(){function t(){}return t.getX=function(t){return"touchmove"===t.type?t.touches[0].clientX:t.clientX},t.getY=function(t){return"touchmove"===t.type?t.touches[0].clientY:t.clientY},t}(),s="acp-hts-wrapper",a="-overflow",c="-more",l="-less",d=function(){function t(t){this.enabled=!1,this.table=t,this.wrapper=null,this.initEvents()}return t.prototype.enable=function(){this.enabled=!0,this.checkWrapperState()},t.prototype.disable=function(){this.enabled=!1},t.prototype.initEvents=function(){var t=this;this.table.addEventListener("scroll",(function(){t.checkWrapperState()})),window.addEventListener("resize",(function(){clearTimeout(t.timeout),t.timeout=setTimeout((function(){t.checkWrapperState()}),100)}))},t.prototype.wrap=function(){this.table.parentElement.classList.contains(s)||(this.wrapper=document.createElement("div"),this.wrapper.classList.add(s),this.table.parentNode.insertBefore(this.wrapper,this.table),this.wrapper.appendChild(this.table))},t.prototype.setOffset=function(t){var e=(this.table.scrollWidth-this.table.offsetWidth)*(t/100);this.table.scrollLeft=Math.round(e)},t.prototype.getOffsetPercentage=function(){var t=this.table.scrollWidth-this.table.offsetWidth,e=this.table.scrollLeft/t*100;return Math.round(e)},t.prototype.checkWrapperState=function(){if(this.enabled){var t=this.table.scrollWidth,e=this.table.offsetWidth,i=this.table.scrollLeft;this.wrapper.classList.remove(l,c,a),t>e&&(this.wrapper.classList.add(a),i+e+10<t&&this.wrapper.classList.add(c),i>0&&this.wrapper.classList.add(l))}},t}(),u=i(4),p=i.n(u),h=function(){function t(t){this.HorizontalScrolling=t,this.timeout=0,this.scrolling=!1,this.offset=0,this.init(),this.initPosition()}return t.prototype.init=function(){var t=this;window.addEventListener("beforeunload",(function(){return t.storeCurrentPosition()}))},t.prototype.getCacheKey=function(){return"hsoffset_"+AC.list_screen+"_"+AC.layout},t.prototype.storeCurrentPosition=function(){var t=new Date((new Date).getTime()+5e3);p.a.set(this.getCacheKey(),this.HorizontalScrolling.getScrollLeft(),{expires:t})},t.prototype.initPosition=function(){var t=this,e=p.a.get(this.getCacheKey());e&&setTimeout((function(){return t.HorizontalScrolling.setScrollLeft(e)}),100)},t}(),f="acp-overflow-table",m=function(){function t(t){this.enabled=document.body.classList.contains(f),this.table=t,this.wrapper=new d(this.table),this.indicator=new n(this.table,this.wrapper),this.scrollPreference=new h(this),this.isEnabled()&&(this.wrapper.wrap(),this.wrapper.enable(),ACP_Horizontal_Scrolling.hasOwnProperty("indicator_enabled")&&ACP_Horizontal_Scrolling.indicator_enabled&&this.indicator.enable())}return t.prototype.isEnabled=function(){return this.enabled},t.prototype.enable=function(){document.body.classList.add(f),this.enabled=!0,this.wrapper.wrap(),this.wrapper.enable(),this.wrapper.checkWrapperState(),this.indicator.enable()},t.prototype.disable=function(){this.wrapper.disable(),this.enabled=!1,this.indicator.disable(),document.body.classList.remove(f)},t.prototype.getScrollLeft=function(){return this.table.scrollLeft},t.prototype.setScrollLeft=function(t){this.table.scrollLeft=t},t.prototype.store=function(){jQuery.post(ajaxurl,{action:"acp_update_table_option_overflow",value:this.isEnabled(),layout:AC.layout,list_screen:AC.list_screen,_ajax_nonce:AC.ajax_nonce})},t}();jQuery(document).ready((function(){var t=document.querySelector(".wp-list-table");if(t){var e=new m(t);jQuery("#acp_overflow_list_screen_table-yes").on("click",(function(){jQuery(this).is(":checked")?(e.enable(),e.store()):(e.disable(),e.store())})),AdminColumns.HorizontalScrolling=e}}))},4:function(t,e,i){var n,r;
/*!
 * JavaScript Cookie v2.2.1
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */!function(o){if(void 0===(r="function"==typeof(n=o)?n.call(e,i,e,t):n)||(t.exports=r),!0,t.exports=o(),!!0){var s=window.Cookies,a=window.Cookies=o();a.noConflict=function(){return window.Cookies=s,a}}}((function(){function t(){for(var t=0,e={};t<arguments.length;t++){var i=arguments[t];for(var n in i)e[n]=i[n]}return e}function e(t){return t.replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent)}return function i(n){function r(){}function o(e,i,o){if("undefined"!=typeof document){"number"==typeof(o=t({path:"/"},r.defaults,o)).expires&&(o.expires=new Date(1*new Date+864e5*o.expires)),o.expires=o.expires?o.expires.toUTCString():"";try{var s=JSON.stringify(i);/^[\{\[]/.test(s)&&(i=s)}catch(t){}i=n.write?n.write(i,e):encodeURIComponent(String(i)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),e=encodeURIComponent(String(e)).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent).replace(/[\(\)]/g,escape);var a="";for(var c in o)o[c]&&(a+="; "+c,!0!==o[c]&&(a+="="+o[c].split(";")[0]));return document.cookie=e+"="+i+a}}function s(t,i){if("undefined"!=typeof document){for(var r={},o=document.cookie?document.cookie.split("; "):[],s=0;s<o.length;s++){var a=o[s].split("="),c=a.slice(1).join("=");i||'"'!==c.charAt(0)||(c=c.slice(1,-1));try{var l=e(a[0]);if(c=(n.read||n)(c,l)||e(c),i)try{c=JSON.parse(c)}catch(t){}if(r[l]=c,t===l)break}catch(t){}}return t?r[t]:r}}return r.set=o,r.get=function(t){return s(t,!1)},r.getJSON=function(t){return s(t,!0)},r.remove=function(e,i){o(e,"",t(i,{expires:-1}))},r.defaults={},r.withConverter=i,r}((function(){}))}))}});