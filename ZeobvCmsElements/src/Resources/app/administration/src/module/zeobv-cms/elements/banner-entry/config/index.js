import template from './zeobv-cms-el-config-product-banner-banner-entry.html.twig';

const { Component, Mixin } = Shopware;

Component.register('zeobv-cms-el-config-product-banner-banner-entry', {
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
            this.initElementConfig('banner-entry');
        }
    },
});
