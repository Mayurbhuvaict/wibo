!function(e){var t={};function r(o){if(t[o])return t[o].exports;var n=t[o]={i:o,l:!1,exports:{}};return e[o].call(n.exports,n,n.exports,r),n.l=!0,n.exports}r.m=e,r.c=t,r.d=function(e,t,o){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(r.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)r.d(o,n,function(t){return e[t]}.bind(null,n));return o},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="/bundles/zeobvschouwwitgoed/",r(r.s="s54e")}({Mkes:function(e,t){var r=Shopware.Component,o=Shopware.Data.Criteria;r.override("sw-cms-detail",{computed:{loadPageCriteria:function(){var e=new o(1,1),t=o.sort("position","ASC",!0);return e.addAssociation("categories").addAssociation("landingPages").getAssociation("sections").addSorting(t).addAssociation("backgroundMedia").getAssociation("blocks").addSorting(t).addAssociation("backgroundMedia").addAssociation("slots"),e}}})},e4RE:function(e,t){var r=Shopware.Component,o=Shopware.Data.Criteria;r.override("sw-cms-list",{computed:{listCriteria:function(){var e=new o(this.page,this.limit);return e.addAssociation("previewMedia").addSorting(o.sort(this.sortBy,this.sortDirection)),null!==this.term&&e.setTerm(this.term),null!==this.currentPageType&&e.addFilter(o.equals("cms_page.type",this.currentPageType)),this.addLinkedLayoutsAggregation(e),e}}})},s54e:function(e,t,r){"use strict";r.r(t);r("e4RE"),r("Mkes")}});