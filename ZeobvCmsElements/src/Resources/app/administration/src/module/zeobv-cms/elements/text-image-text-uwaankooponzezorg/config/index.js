import template from './sw-cms-el-config-text-image-text-uwaankooponzezorg.html.twig';
import './sw-cms-el-config-text-image-text-uwaankooponzezorg.scss';

const { Component, Mixin } = Shopware;
Component.register('sw-cms-el-config-text-image-text-uwaankooponzezorg', {
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
            alert("Hey Wibo");
            this.initElementConfig('text-image-text-uwaankooponzezorg');
        },
    },
});
