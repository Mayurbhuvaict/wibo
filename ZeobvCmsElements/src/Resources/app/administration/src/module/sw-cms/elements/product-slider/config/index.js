import template from './sw-cms-el-config-product-slider.html.twig';
import './sw-cms-el-config-product-slider.scss';

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.override('sw-cms-el-config-product-slider', {
    template,
    methods:{
        enableSlider(value){
            this.element.config.enableSlider = {
                'source':'static',
                'value':value
            };
        }
    }
});
