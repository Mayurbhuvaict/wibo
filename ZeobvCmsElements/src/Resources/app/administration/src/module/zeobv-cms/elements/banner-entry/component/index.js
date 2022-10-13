import template from './zeobv-cms-el-product-banner-banner-entry.html.twig';
import './zeobv-cms-el-product-banner-banner-entry.scss';

const { Component, Mixin } = Shopware;

Component.register('zeobv-cms-el-product-banner-banner-entry', {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('banner-entry');
        },
    },
});
