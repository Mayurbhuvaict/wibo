(this.webpackJsonp=this.webpackJsonp||[]).push([["zeo-experience-block-plugin"],{"/nC2":function(e,i,t){"use strict";t.r(i);var n=t("z4Ug"),o=t.n(n);t("Drao");const{Component:a,Mixin:s}=Shopware;a.extend("sw-cms-el-image-caption","sw-cms-el-image",{template:o.a,data:()=>({editable:!0,demoValue:""}),computed:{captionStyles(){return{background:0!==this.element.config.captionBackgroundColor.value?this.element.config.captionBackgroundColor.value:"#D1D9E0"}},captionClasses(){return["sw-cms-el-image-caption__caption-"+this.element.config.captionPosition.value]}},watch:{cmsPageState:{deep:!0,handler(){this.updateDemoValue(),this.$forceUpdate()}},"element.config.caption.source":{handler(){this.updateDemoValue()}}},created(){this.createdComponent()},methods:{createdComponent(){this.$super("createdComponent"),this.initElementConfig("text")},updateDemoValue(){"mapped"===this.element.config.caption.source&&(this.demoValue=this.getDemoValue(this.element.config.caption.value))},onBlur(e){this.emitChanges(e)},onInput(e){this.emitChanges(e)},emitChanges(e){e!==this.element.config.caption.value&&(this.element.config.caption.value=e,this.$emit("element-update",this.element))}}});var c=t("YpB1"),l=t.n(c);t("F4AR");const{Component:m}=Shopware;m.extend("sw-cms-el-config-image-caption","sw-cms-el-config-image",{template:l.a,methods:{createdComponent(){this.$super("createdComponent"),this.initElementConfig("text")},onBlur(e){this.emitCaptionChanges(e)},onInput(e){this.emitCaptionChanges(e)},onChangeCaptionPosition(e){this.$emit("element-update",this.element)},emitCaptionChanges(e){e!==this.element.config.caption.value&&(this.element.config.caption.value=e,this.$emit("element-update",this.element))}}});var p=t("qV36"),r=t.n(p);t("zvUd");const{Component:g}=Shopware;g.register("sw-cms-el-preview-image-caption",{template:r.a}),Shopware.Service("cmsService").registerCmsElement({name:"image-caption",label:"sw-cms.elements.image-caption.label",component:"sw-cms-el-image-caption",configComponent:"sw-cms-el-config-image-caption",previewComponent:"sw-cms-el-preview-image-caption",defaultConfig:{media:{source:"static",value:null,required:!0,entity:{name:"media"}},displayMode:{source:"static",value:"standard"},url:{source:"static",value:null},newTab:{source:"static",value:!1},minHeight:{source:"static",value:"340px"},verticalAlign:{source:"static",value:null},caption:{source:"static",value:'<p style="color: #52667A;">Lorem Ipsum dolor</p>'},captionPosition:{source:"static",value:"bottom"},captionBackgroundColor:{source:"static",value:"#D1D9E0"}}});var d=t("5kJT"),w=t.n(d);t("BiMC");const{Component:u}=Shopware;u.register("sw-cms-block-image-description-with-2-horizontal",{template:w.a});var v=t("dghs"),_=t.n(v);t("dpBp");const{Component:h}=Shopware;h.register("sw-cms-preview-image-description-with-2-horizontal",{template:_.a}),Shopware.Service("cmsService").registerCmsBlock({name:"image-description-with-2-horizontal",label:"sw-cms.blocks.text-image.imageDescriptionWith2Horizontal.label",category:"text-image",component:"sw-cms-block-image-description-with-2-horizontal",previewComponent:"sw-cms-preview-image-description-with-2-horizontal",defaultConfig:{marginBottom:"0",marginTop:"0",marginLeft:"0",marginRight:"0",sizingMode:"boxed"},slots:{"big-image":{type:"image-caption",default:{config:{displayMode:{source:"static",value:"cover"},minHeight:{source:"static",value:"200px"}},data:{media:{url:"/administration/static/img/cms/preview_plant_large.jpg"}}}},"side-top":{type:"image",default:{config:{displayMode:{source:"static",value:"cover"}},data:{media:{url:"/administration/static/img/cms/preview_camera_large.jpg"}}}},"side-bottom":{type:"image",default:{config:{displayMode:{source:"static",value:"cover"}},data:{media:{url:"/administration/static/img/cms/preview_glasses_large.jpg"}}}}}});var b=t("K+Ty"),f=t("XBk9");Shopware.Locale.extend("de-DE",b),Shopware.Locale.extend("en-GB",f)},"5kJT":function(e,i){e.exports='{% block sw_cms_block_image_description_with_2_horizontal %}\n    <div class="sw-cms-block-image-description-with-2-horizontal">\n        <div class="sw-cms-block-image-description-with-2-horizontal__big-image">\n            <slot name="big-image"></slot>\n        </div>\n\n        <div class="sw-cms-block-image-description-with-2-horizontal__side-images">\n            <div class="sw-cms-block-image-description-with-2-horizontal__image-top">\n                <slot name="side-top"></slot>\n            </div>\n            <div class="sw-cms-block-image-description-with-2-horizontal__image-bottom">\n                <slot name="side-bottom"></slot>\n            </div>\n        </div>\n    </div>\n{% endblock %}\n\n'},BiMC:function(e,i,t){var n=t("wMWY");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);(0,t("SZ7m").default)("ccc7c7c6",n,!0,{})},Drao:function(e,i,t){var n=t("T96R");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);(0,t("SZ7m").default)("e60032f8",n,!0,{})},F4AR:function(e,i,t){var n=t("nc6v");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);(0,t("SZ7m").default)("437b95e7",n,!0,{})},"K+Ty":function(e){e.exports=JSON.parse('{"sw-cms":{"elements":{"image-caption":{"label":"Bild","tab":{"image":"Image","caption":"Caption"},"config":{"label":{"minHeight":"Minimale Höhe","linkTo":"Link zu","linkNewTab":"Link in neuem Tab öffnen","captionBackgroundColor":"Caption background","captionPosition":"Position"},"placeholder":{"enterMinHeight":"Minimale Höhe eingeben ...","enterUrl":"URL eingeben ..."},"option":{"captionPosition":{"top":"Top","bottom":"Bottom"}}}}},"blocks":{"text-image":{"imageDescriptionWith2Horizontal":{"label":"Image + description, with 2 horizontal images"}}}}}')},T96R:function(e,i,t){},XBk9:function(e){e.exports=JSON.parse('{"sw-cms":{"elements":{"image-caption":{"label":"Image with text","tab":{"image":"Image","caption":"Caption"},"config":{"label":{"minHeight":"Minimum height","linkTo":"Link to","linkNewTab":"Open link in new tab","captionBackgroundColor":"Caption background","captionPosition":"Position"},"placeholder":{"enterMinHeight":"Enter a minimum height...","enterUrl":"Enter URL..."},"option":{"captionPosition":{"top":"Top","bottom":"Bottom"}}}}},"blocks":{"text-image":{"imageDescriptionWith2Horizontal":{"label":"Image + description, with 2 horizontal images"}}}}}')},YpB1:function(e,i){e.exports='{% block sw_cms_element_image_config %}\n<sw-tabs class="sw-cms-el-config-text__tabs" defaultItem="image">\n\n    <template slot-scope="{ active }" >\n        {% block sw_cms_el_config_text_tab_image %}\n        <sw-tabs-item :title="$tc(\'sw-cms.elements.image-caption.tab.image\')"\n                      name="image"\n                      :activeTab="active">\n            {{ $tc(\'sw-cms.elements.image-caption.tab.image\') }}\n        </sw-tabs-item>\n        {% endblock %}\n        {% block sw_cms_el_text_config_tab_caption %}\n        <sw-tabs-item :title="$tc(\'sw-cms.elements.image-caption.tab.caption\')"\n                      name="caption"\n                      :activeTab="active">\n            {{ $tc(\'sw-cms.elements.image-caption.tab.caption\') }}\n        </sw-tabs-item>\n        {% endblock %}\n    </template>\n\n    <template slot="content" slot-scope="{ active }">\n        {% block sw_cms_el_text_config_image %}\n        <sw-container v-if="active === \'image\'" class="sw-cms-el-config-text__tab-image">\n            {% parent %}\n        </sw-container>\n        {% endblock %}\n\n        {% block sw_cms_el_text_config_caption %}\n        <sw-container v-if="active === \'caption\'" class="sw-cms-el-config-text__tab-caption">\n            <sw-cms-mapping-field :label="$tc(\'sw-cms.elements.image-caption.tab.caption\')" valueTypes="string" v-model="element.config.caption">\n                {% block sw_cms_el_text_config_caption_caption_editor%}\n                <sw-text-editor v-model="element.config.caption.value"\n                                @input="onInput"\n                                @blur="onBlur">\n                </sw-text-editor>\n\n                <div class="sw-cms-el-config-text__mapping-preview" slot="preview" slot-scope="{ demoValue }">\n                    <div v-html="$sanitize(demoValue)"></div>\n                </div>\n                {% endblock %}\n\n                {% block sw_cms_el_text_config_caption_caption_background_color%}\n                <sw-colorpicker :label="$tc(\'sw-cms.elements.image-caption.config.label.captionBackgroundColor\')"\n                                v-model="element.config.captionBackgroundColor.value"\n                                colorOutput="hex"\n                                :zIndex="1001"\n                                :alpha="true">\n                </sw-colorpicker>\n                {% endblock %}\n\n                {% block sw_cms_el_text_config_caption_caption_position %}\n                <sw-select-field :label="$tc(\'sw-cms.elements.image-caption.config.label.captionPosition\')"\n                                 v-model="element.config.captionPosition.value"\n                                 @change="onChangeCaptionPosition">\n                    <option value="top">{{ $tc(\'sw-cms.elements.image-caption.config.option.captionPosition.top\') }}</option>\n                    <option value="bottom">{{ $tc(\'sw-cms.elements.image-caption.config.option.captionPosition.bottom\') }}</option>\n                </sw-select-field>\n                {% endblock %}\n\n            </sw-cms-mapping-field>\n        </sw-container>\n        {% endblock %}\n    </template>\n</sw-tabs>\n\n\n\n{% endblock %}\n'},dghs:function(e,i){e.exports='{% block sw_cms_block_image_description_with_2_horizontal_preview %}\n    <div class="sw-cms-preview-image-description-with-2-horizontal">\n        <div class="sw-cms-preview-image-description-with-2-horizontal__big-image">\n            <img :src="\'/administration/static/img/cms/preview_plant_small.jpg\' | asset">\n\n            <div class="sw-cms-preview-image-description-with-2-horizontal__big-image-caption">\n                Test banner\n            </div>\n        </div>\n\n        <div class="sw-cms-preview-image-description-with-2-horizontal__side-images">\n            <div class="sw-cms-preview-image-description-with-2-horizontal__image-top">\n                <img :src="\'/administration/static/img/cms/preview_camera_small.jpg\' | asset">\n            </div>\n            <div class="sw-cms-preview-image-description-with-2-horizontal__image-bottom">\n                <img :src="\'/administration/static/img/cms/preview_glasses_small.jpg\' | asset">\n            </div>\n        </div>\n    </div>\n{% endblock %}\n'},dpBp:function(e,i,t){var n=t("puok");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);(0,t("SZ7m").default)("23c0b2f0",n,!0,{})},gKIp:function(e,i,t){},nc6v:function(e,i,t){},puok:function(e,i,t){},qV36:function(e,i){e.exports='{% block sw_cms_element_image_caption_preview %}\n    <div class="sw-cms-el-preview-image-caption">\n        <img :src="\'/administration/static/img/cms/preview_mountain_small.jpg\' | asset">\n\n        <div class="sw-cms-el-preview-image-caption__text sw-cms-el-preview-image-caption__text-bottom" style="background:#D1D9E0">\n            <p style="color: #52667A;">Lorem Ipsum dolor</p>\n        </div>\n    </div>\n{% endblock %}\n'},wMWY:function(e,i,t){},z4Ug:function(e,i){e.exports='{% block sw_cms_element_image %}\n    <div class="sw-cms-el-image-caption" :class="displayModeClass" :style="styles">\n        <img :src="mediaUrl" class="sw-cms-el-image-caption__image">\n\n        {% block sw_cms_element_text %}\n            <div class="sw-cms-el-image-caption__caption" :class="captionClasses" :style="captionStyles">\n                <div class="sw-cms-el-text__mapping-preview content-editor"\n                     v-html="$sanitize(demoValue)"\n                     v-if="element.config.caption.source === \'mapped\'">\n                </div>\n\n                <sw-text-editor v-else\n                                v-model="element.config.caption.value"\n                                @blur="onBlur"\n                                @input="onInput"\n                                :verticalAlign="element.config.verticalAlign.value"\n                                :isInlineEdit="true">\n                </sw-text-editor>\n            </div>\n        {% endblock %}\n    </div>\n{% endblock %}\n'},zvUd:function(e,i,t){var n=t("gKIp");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);(0,t("SZ7m").default)("725054b9",n,!0,{})}},[["/nC2","runtime","vendors-node"]]]);