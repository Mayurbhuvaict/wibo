import template from './sw-cms-el-text-icon.html.twig';
import './sw-cms-el-text-icon.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-text-icon', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    computed: {
        layoutClass(){
            return {
                ['sw-cms-el-text-icon__layout-' + this.element.config.layout.value] : this.element.config.layout.value
            }
        }
    },

    methods: {
        createdComponent() {
            this.initElementConfig('text-icon');
        },

        onContentBlur(content) {
            this.emitContentChanges(content);
        },

        onContentInput(content) {
            this.emitContentChanges(content);
        },

        emitContentChanges(content) {
            if (content !== this.element.config.content.value) {
                this.element.config.content.value = content;
                this.$emit('element-update', this.element);
            }
        },
    }
});
