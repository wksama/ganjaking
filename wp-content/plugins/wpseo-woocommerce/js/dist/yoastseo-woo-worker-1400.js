!function(e){var t={};function r(s){if(t[s])return t[s].exports;var o=t[s]={i:s,l:!1,exports:{}};return e[s].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=e,r.c=t,r.d=function(e,t,s){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:s})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var s=Object.create(null);if(r.r(s),Object.defineProperty(s,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)r.d(s,o,function(t){return e[t]}.bind(null,o));return s},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="",r(r.s=5)}({0:function(e,t){e.exports=yoast.analysis},5:function(e,t,r){"use strict";r.r(t);var s=r(0);const{stripHTMLTags:o}=s.string;class i extends s.Assessment{constructor(e,t){super(),this._l10n=t,this.updateProductDescription(e)}updateProductDescription(e){this._productDescription=e}getResult(){const e=this._productDescription,t=o(e).split(" ").length,r=this.scoreProductDescription(t),i=new s.AssessmentResult;return i.setScore(r.score),i.setText(r.text),i}scoreProductDescription(e){return 0===e?{score:1,text:this._l10n.woo_desc_none}:e>0&&e<20?{score:5,text:this._l10n.woo_desc_short}:e>=20&&e<=50?{score:9,text:this._l10n.woo_desc_good}:e>50?{score:5,text:this._l10n.woo_desc_long}:void 0}}const n="YoastWooCommerce",c="productTitle";(new class{constructor(){this._worker=analysisWorker}register(){this._worker.registerMessageHandler("initialize",this.initialize.bind(this),n),this._worker.registerMessageHandler("updateProductDescription",this.updateProductDescription.bind(this),n)}initialize({productDescription:e,l10n:t}){this._productDescriptionAssessment=new i(e,t),this._worker.registerAssessment(c,this._productDescriptionAssessment,n),this._worker.refreshAssessment(c,n)}updateProductDescription(e){this._productDescriptionAssessment.updateProductDescription(e),this._worker.refreshAssessment(c,n)}}).register()}});