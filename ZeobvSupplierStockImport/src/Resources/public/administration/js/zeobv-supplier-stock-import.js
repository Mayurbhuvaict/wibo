!function(e){var t={};function n(a){if(t[a])return t[a].exports;var i=t[a]={i:a,l:!1,exports:{}};return e[a].call(i.exports,i,i.exports,n),i.l=!0,i.exports}n.m=e,n.c=t,n.d=function(e,t,a){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:a})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(n.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)n.d(a,i,function(t){return e[t]}.bind(null,i));return a},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/bundles/zeobvsupplierstockimport/",n(n.s="214n")}({"214n":function(e,t,n){"use strict";n.r(t);var a=n("RZFV"),i=n.n(a),r=(n("2xFo"),n("JZXW")),s=n("69yv");function o(e,t,n,a,i,r,s){try{var o=e[r](s),c=o.value}catch(e){return void n(e)}o.done?t(c):Promise.resolve(c).then(a,i)}var c=Shopware,l=c.Component,d=c.Mixin;l.register("stock-import-list",{template:i.a,inject:["configService"],mixins:[d.getByName("notification")],data:function(){return{}},snippets:{"de-DE":r,"en-GB":s},created:function(){this.createdComponent()},methods:{createdComponent:function(){return(e=regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:case"end":return e.stop()}}),e)})),function(){var t=this,n=arguments;return new Promise((function(a,i){var r=e.apply(t,n);function s(e){o(r,a,i,s,c,"next",e)}function c(e){o(r,a,i,s,c,"throw",e)}s(void 0)}))})();var e},onAtagSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/atagsupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onEtnaSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/etnasupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onAskoSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/askosupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onPelgrimSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/pelgrimsupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onHisenseSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/atagsupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onAmacomSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/hisensesupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onBorettiSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/borettisupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onInventumSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/inventumsupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))},onSmegSave:function(){var e=this,t=this.configService.getBasicHeaders();return this.configService.httpClient.get("/zeostock/smegsupplier",{headers:t}).then((function(t){"error"!==t.data.type?e.createNotificationSuccess({title:t.data.type,message:t.data.message}):e.createNotificationError({title:t.data.type,message:t.data.message})}))}}}),Shopware.Module.register("stock-import",{type:"plugin",name:"stock-import.general.mainMenuItemGeneral",title:"stock-import.general.mainMenuItemGeneral",description:"stock-import.general.mainMenuItemGeneral",color:"#ff3d58",icon:"default-action-cloud-download",routes:{list:{component:"stock-import-list",path:"list"}},navigation:[{id:"stock-import-list",label:"stock-import.general.mainMenuItemGeneral",parent:"sw-catalogue",path:"stock.import.list",position:49,color:"#57d9a3"}],settingsItem:{group:"plugins",to:"stock.import.list",icon:"default-text-code",backgroundEnabled:!0}})},"2xFo":function(e,t,n){var a=n("Edhm");"string"==typeof a&&(a=[[e.i,a,""]]),a.locals&&(e.exports=a.locals);(0,n("SZ7m").default)("a8bbcf62",a,!0,{})},"69yv":function(e){e.exports=JSON.parse('{"stock-import":{"general":{"mainMenuItemGeneral":"Stock Import"},"label":{"title":"Stock Import","amacomBtn":"Amacom Supplier","borettiBtn":"Boretti Supplier","inventumBtn":"Inventum Supplier","smegBtn":"Smeg Supplier","atagBtn":"Atag Supplier","etnaBtn":"Etna Supplier","pelgrimBtn":"Pelgrim Supplier","askoBtn":"Asko Supplier","hisenseBtn":"Hisense Supplier"}}}')},Edhm:function(e,t,n){},JZXW:function(e){e.exports=JSON.parse('{"stock-import":{"general":{"mainMenuItemGeneral":"Stock Import"},"label":{"title":"Stock Import","aepBtn":"AEP Supplier","amacomBtn":"Amacom Supplier"}}}')},RZFV:function(e,t){e.exports='{% block stock_import_list %}\n    <sw-page class="stock-import-list">\n        <template slot="content">\n            <sw-card class="sw-settings-shipping-detail__condition_container">\n                <div class="collection-container">\n                    <div style="width:100%;">\n                        <h1>{{ $t(\'stock-import.label.title\') }}</h1>\n                    </div>\n                    <div class="row mb-3">\n                        <div class="col-md-4">\n                            <sw-button id="atagImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onAtagSave">\n                                {{ $t(\'stock-import.label.atagBtn\') }}\n                            </sw-button>\n                        </div>\n\n                        <div class="col-md-4">\n                            <sw-button id="etnaImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onEtnaSave">\n                                {{ $t(\'stock-import.label.etnaBtn\') }}\n                            </sw-button>\n                        </div>\n\n                        <div class="col-md-4">\n                            <sw-button id="askoImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onAskoSave">\n                                {{ $t(\'stock-import.label.askoBtn\') }}\n                            </sw-button>\n                        </div>\n                    </div>\n\n                    <div class="row mb-3">\n                        <div class="col-md-4">\n                            <sw-button id="pelgrimImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onPelgrimSave">\n                                {{ $t(\'stock-import.label.pelgrimBtn\') }}\n                            </sw-button>\n                        </div>\n                        <div class="col-md-4">\n                            <sw-button id="hisenseImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onHisenseSave">\n                                {{ $t(\'stock-import.label.hisenseBtn\') }}\n                            </sw-button>\n                        </div>\n                        <div class="col-md-4">\n                            <sw-button id="amacomImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onAmacomSave">\n                                {{ $t(\'stock-import.label.amacomBtn\') }}\n                            </sw-button>\n                        </div>\n\n                    </div>\n\n                    <div class="row">\n                        <div class="col-md-4">\n                            <sw-button id="borettiImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onBorettiSave">\n                                {{ $t(\'stock-import.label.borettiBtn\') }}\n                            </sw-button>\n                        </div>\n                        <div class="col-md-4">\n                            <sw-button id="inventumImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onInventumSave">\n                                {{ $t(\'stock-import.label.inventumBtn\') }}\n                            </sw-button>\n                        </div>\n                        <div class="col-md-4">\n                            <sw-button id="smegImportButton"\n                                       :disabled="false"\n                                       variant="primary"\n                                       :square="false"\n                                       :block="false"\n                                       :isLoading="false"\n                                       @click="onSmegSave">\n                                {{ $t(\'stock-import.label.smegBtn\') }}\n                            </sw-button>\n                        </div>\n                    </div>\n                </div>\n            </sw-card>\n        </template>\n    </sw-page>\n{% endblock %}\n'},SZ7m:function(e,t,n){"use strict";function a(e,t){for(var n=[],a={},i=0;i<t.length;i++){var r=t[i],s=r[0],o={id:e+":"+i,css:r[1],media:r[2],sourceMap:r[3]};a[s]?a[s].parts.push(o):n.push(a[s]={id:s,parts:[o]})}return n}n.r(t),n.d(t,"default",(function(){return f}));var i="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!i)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var r={},s=i&&(document.head||document.getElementsByTagName("head")[0]),o=null,c=0,l=!1,d=function(){},u=null,p="data-vue-ssr-id",m="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function f(e,t,n,i){l=n,u=i||{};var s=a(e,t);return g(s),function(t){for(var n=[],i=0;i<s.length;i++){var o=s[i];(c=r[o.id]).refs--,n.push(c)}t?g(s=a(e,t)):s=[];for(i=0;i<n.length;i++){var c;if(0===(c=n[i]).refs){for(var l=0;l<c.parts.length;l++)c.parts[l]();delete r[c.id]}}}}function g(e){for(var t=0;t<e.length;t++){var n=e[t],a=r[n.id];if(a){a.refs++;for(var i=0;i<a.parts.length;i++)a.parts[i](n.parts[i]);for(;i<n.parts.length;i++)a.parts.push(h(n.parts[i]));a.parts.length>n.parts.length&&(a.parts.length=n.parts.length)}else{var s=[];for(i=0;i<n.parts.length;i++)s.push(h(n.parts[i]));r[n.id]={id:n.id,refs:1,parts:s}}}}function v(){var e=document.createElement("style");return e.type="text/css",s.appendChild(e),e}function h(e){var t,n,a=document.querySelector("style["+p+'~="'+e.id+'"]');if(a){if(l)return d;a.parentNode.removeChild(a)}if(m){var i=c++;a=o||(o=v()),t=y.bind(null,a,i,!1),n=y.bind(null,a,i,!0)}else a=v(),t=k.bind(null,a),n=function(){a.parentNode.removeChild(a)};return t(e),function(a){if(a){if(a.css===e.css&&a.media===e.media&&a.sourceMap===e.sourceMap)return;t(e=a)}else n()}}var b,S=(b=[],function(e,t){return b[e]=t,b.filter(Boolean).join("\n")});function y(e,t,n,a){var i=n?"":a.css;if(e.styleSheet)e.styleSheet.cssText=S(t,i);else{var r=document.createTextNode(i),s=e.childNodes;s[t]&&e.removeChild(s[t]),s.length?e.insertBefore(r,s[t]):e.appendChild(r)}}function k(e,t){var n=t.css,a=t.media,i=t.sourceMap;if(a&&e.setAttribute("media",a),u.ssrId&&e.setAttribute(p,t.id),i&&(n+="\n/*# sourceURL="+i.sources[0]+" */",n+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(i))))+" */"),e.styleSheet)e.styleSheet.cssText=n;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(n))}}}});