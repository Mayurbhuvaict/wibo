import template from './sw-cms-el-config-product-slider.html.twig';
import './sw-cms-el-config-product-slider.scss';

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.override('sw-cms-el-config-product-slider', {
    template,

    created() {
        this.createdComponentExtra();
    },
    methods:{
        async createdComponentExtra() {
            const extraConfig = {
                enableSlider: {
                    source: 'static',
                    value: false
                },
            }
            this.element.config = Object.assign(extraConfig, this.element.config);
        },
        enableSlider(value){
            this.element.config.enableSlider = {
                'source':'static',
                'value':value
            };
        }
    }
});
