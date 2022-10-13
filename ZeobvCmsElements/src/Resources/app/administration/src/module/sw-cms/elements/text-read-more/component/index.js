import template from './sw-cms-el-text-read-more.html.twig';
import './sw-cms-el-text-read-more.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-text-read-more', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            expandedContentVisible: false
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('text-read-more');
        },

        toggleExpandedContent() {
            this.expandedContentVisible = !this.expandedContentVisible
        }
    }
});
