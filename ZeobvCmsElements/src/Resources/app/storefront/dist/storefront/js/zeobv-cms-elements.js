(window.webpackJsonp=window.webpackJsonp||[]).push([["zeobv-cms-elements"],{"1dFS":function(e,t,n){"use strict";n.r(t);var o=n("FGIj");n("k8s9");function r(e){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function i(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function u(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}function c(e,t){return!t||"object"!==r(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function s(e){return(s=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function a(e,t){return(a=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}var l=function(e){function t(){return i(this,t),c(this,s(t).apply(this,arguments))}var n,o,r;return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&a(e,t)}(t,e),n=t,(o=[{key:"init",value:function(){this.expandButton=this.el.querySelector(".cms-element-text-read-more__expand-button"),this.collapseButton=this.el.querySelector(".cms-element-text-read-more__collapse-button"),this._registerEvents()}},{key:"_registerEvents",value:function(){var e=this;this.expandButton.addEventListener("click",(function(t){e.el.classList.add("expanded")})),this.collapseButton.addEventListener("click",(function(t){e.el.classList.remove("expanded")}))}}])&&u(n.prototype,o),r&&u(n,r),t}(o.a);window.PluginManager.register("TextReadMore",l,"[data-cms-text-read-more]")}},[["1dFS","runtime","vendor-node","vendor-shared"]]]);