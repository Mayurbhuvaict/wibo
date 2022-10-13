import template from './zeobv-cms-el-product-banner-cta.html.twig';
import './zeobv-cms-el-product-banner-cta.scss';

const { Component, Mixin } = Shopware;

Component.register('zeobv-cms-el-product-banner-cta', {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('product-banner-cta');
        },
    },
});
