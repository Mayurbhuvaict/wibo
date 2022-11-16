import template from './sw-cms-el-config-text-read-more.html.twig';
import './sw-cms-el-config-text-read-more.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-text-read-more', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('text-read-more');
        },
    }
});
