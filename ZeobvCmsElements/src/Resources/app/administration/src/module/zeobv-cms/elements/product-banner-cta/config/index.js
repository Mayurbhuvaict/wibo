import template from './zeobv-cms-el-config-product-banner-cta.html.twig';

const { Component, Mixin } = Shopware;

Component.register('zeobv-cms-el-config-product-banner-cta', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('product-banner-cta');
        }
    },
});
