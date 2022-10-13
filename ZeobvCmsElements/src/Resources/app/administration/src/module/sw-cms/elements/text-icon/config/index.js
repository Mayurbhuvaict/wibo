import template from './sw-cms-el-config-text-icon.html.twig';
import './sw-cms-el-config-text-icon.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-text-icon', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('text-icon');
        },

        onLayoutChanged(layout) {
            this.element.config.layout.value = layout;

            this.$emit('element-update', this.element);
        },

        onChangeTooltip(tooltip) {
            this.element.config.tooltip.value = tooltip;

            this.$emit('element-update', this.element);
        },

        onChangeIconSize(iconSize) {
            if(!iconSize.endsWith('px')){
                return;
            }

            this.element.config.iconSize.value = iconSize;

            this.$emit('element-update', this.element);
        },

        onIconChanged(iconEvent) {
            this.element.config.icon.value = iconEvent.icon;
            this.element.config.iconFamily.value = iconEvent.family;
            this.element.config.svg.value = iconEvent.svg;

            this.$emit('element-update', this.element);
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
