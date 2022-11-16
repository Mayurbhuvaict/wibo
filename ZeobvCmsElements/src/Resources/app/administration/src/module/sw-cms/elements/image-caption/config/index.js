import template from './sw-cms-el-config-image-caption.html.twig';
import './sw-cms-el-config-image-caption.scss';

const { Component } = Shopware;

Component.extend('sw-cms-el-config-image-caption', 'sw-cms-el-config-image', {
    template,

    methods: {
        createdComponent() {
            this.$super('createdComponent');

            this.initElementConfig('image-caption');
        },

        onBlur(caption) {
            this.emitCaptionChanges(caption);
        },

        onInput(caption) {
            this.emitCaptionChanges(caption);
        },

        onChangeCaptionPosition(position) {
            this.$emit('element-update', this.element);
        },

        emitCaptionChanges(caption) {
            if (caption !== this.element.config.caption.value) {
                this.element.config.caption.value = caption;
                this.$emit('element-update', this.element);
            }
        }
    }
});
