import template from './sw-cms-el-text-product-video.html.twig';
import './sw-cms-el-text-product-video.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-text-product-video', {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            editable: true,
            demoValue: '',
        };
    },

    computed: {
        videoID() {
            return this.element.config.videoID.value;
        },

        relatedVideos() {
            return 'rel=0&';
        },

        loop() {
            if (!this.element.config.loop.value && !this.element.config.videoType.value) {
                return '';
            }
            if(this.element.config.videoType.value === 'youtube') {
                return `loop=1&playlist=${this.videoID}&`;
            }
            if(this.element.config.videoType.value === 'vimeo') {
                return `loop=${this.element.config.loop.value}&`;
            }
        },

        showControls() {
            if (this.element.config.showControls.value) {
                return '';
            }

            return 'controls=0&';
        },

        disableKeyboard() {
            return 'disablekb=1';
        },

        videoUrl() {
            var checkVideoType = this.element.config.videoType.value;
            if(!checkVideoType) {
                return 'https://www.youtube-nocookie.com/embed/';
            }
            if(checkVideoType === 'youtube') {
                const url = `https://www.youtube-nocookie.com/embed/\
                ${this.videoID}?\
                ${this.relatedVideos}\
                ${this.loop}\
                ${this.showControls}\
                ${this.disableKeyboard}`.replace(/ /g, '');

                return url;
            }
            if(checkVideoType === 'vimeo') {
                return `https://player.vimeo.com/video/
                ${this.videoID}?\
                ${this.loop}\
                ${this.showControls}\
                ${this.relatedVideos}\
                ${this.disableKeyboard}`.replace(/ /g, '');
            }
        },
    },

    watch: {
        cmsPageState: {
            deep: true,
            handler() {
                this.updateDemoValue();
            },
        },

        'element.config.content.source': {
            handler() {
                this.updateDemoValue();
            },
        },

    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('text-product-video');
            this.initElementData('text-product-video');
        },

        // text field code start
        updateDemoValue() {
            this.demoValue = this.getDemoValue(this.element.config.content.value);

        },

        onBlur(content) {
            this.emitChanges(content);
        },

        onInput(content) {
            this.emitChanges(content);
        },

        emitChanges(content) {
            if (content !== this.element.config.content.value) {
                this.element.config.content.value = content;
                this.$emit('element-update', this.element);
            }

        },
    },
});
