import template from './sw-cms-el-config-category-listing.html.twig';
import './sw-cms-el-config-category-listing.scss';

const { Component, Mixin } = Shopware;

Component.override('sw-cms-el-config-category-listing', {
    template,
    created() {
        this.createdComponentExtra();
    },
    methods:{
        async createdComponentExtra() {
            const extraConfig = {
                enableListSlider: {
                    source: 'static',
                    value: false
                },
            }
            this.element.config = Object.assign(extraConfig, this.element.config);
        },
        enableListSlider(value){
            this.element.config.enableListSlider = {
                'source':'static',
                'value':value
            };
        }
    }
});
