import template from './sw-cms-text-image-layout.html.twig';
import './sw-cms-text-image-layout.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-text-image-layout', {
    template,

    mixins: [
        Mixin.getByName('sw-form-field'),
    ],

    props: {
        selected: {
            type: String,
            required: false,
        },
    },

    data() {
        return {
            selectedLayout: this.selected,
            layouts: {
                'icon-top-text-bottom' : "Icon top, text bottom",
                'icon-bottom-text-top' : "Icon bottom, text top",
                'icon-left-text-right' : "Icon left, text right",
                'icon-right-text-left' : "Icon right, text left",
            }
        }
    },

    methods: {
        selectLayout(newLayout){
            this.selectedLayout = newLayout;

            this.$emit('change', newLayout);
        },

        layoutClasses(layout){
            return {
                [`sw-cms-text-image-layout__layout-${layout}`]: this.layouts[layout] !== undefined,
                'sw-cms-text-image-layout__layout-selected': this.selectedLayout === layout
            };
        }
    },
});

