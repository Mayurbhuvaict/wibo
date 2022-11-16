import template from './sw-cms-el-image-caption.html.twig';
import './sw-cms-el-image-caption.scss';

const { Component, Mixin } = Shopware;

Component.extend('sw-cms-el-image-caption', 'sw-cms-el-image', {
    template,

    data() {
        return {
            editable: true,
            demoValue: ''
        };
    },

    computed: {
        captionStyles() {
            return {
                'background': this.element.config.captionBackgroundColor.value !== 0 ? this.element.config.captionBackgroundColor.value : '#D1D9E0',
            };
        },
        captionClasses() {
            return [
                "sw-cms-el-image-caption__caption-" + this.element.config.captionPosition.value
            ]
        },
    },

    watch: {
        cmsPageState: {
            deep: true,
            handler() {
                this.updateDemoValue();

                this.$forceUpdate();
            }
        },

        'element.config.caption.source': {
            handler() {
                this.updateDemoValue();
            }
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.$super('createdComponent');

            this.initElementConfig('image-caption');
        },

        updateDemoValue() {
            if (this.element.config.caption.source === 'mapped') {
                this.demoValue = this.getDemoValue(this.element.config.caption.value);
            }
        },

        onBlur(caption) {
            this.emitChanges(caption);
        },

        onInput(caption) {
            this.emitChanges(caption);
        },

        emitChanges(caption) {
            if (caption !== this.element.config.caption.value) {
                this.element.config.caption.value = caption;
                this.$emit('element-update', this.element);
            }
        }
    }
});

