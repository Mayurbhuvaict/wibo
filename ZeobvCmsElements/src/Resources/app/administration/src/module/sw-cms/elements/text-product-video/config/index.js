import template from './sw-cms-el-config-text-product-video.html.twig';
import './sw-cms-el-config-text-product-video.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-text-product-video', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            mediaModalIsOpen: false,
            initialFolderId: null,
        };
    },

    computed: {
        // videoID: {
        //     get() {
        //         return this.element.config.videoID.value;
        //     },
        //
        //     set(link) {
        //         this.element.config.videoID.value = this.shortenLink(link);
        //     },
        // },


        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        uploadTag() {
            return `cms-element-text-product-video-config-${this.element.id}`;
        },

        previewSource() {
            if (this.element.data && this.element.data.previewMedia && this.element.data.previewMedia.id) {
                return this.element.data.previewMedia;
            }

            return this.element.config.previewMedia.value;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        setTimeValue(value, type) {
            this.element.config[type].value = this.convertTimeToUrlFormat(value).string;
        },

        createdComponent() {
            this.initElementConfig('text-product-video');
        },

        convertTimeToInputFormat(time) {

            const returnValues = {};
            let incomingTime = time;

            const regex = /^[0-9]*$/;
            const isValidFormat = regex.test(time);

            if (!isValidFormat) {
                incomingTime = 0;
            }

            const minutes = Math.floor(incomingTime / 60);
            let seconds = incomingTime - minutes * 60;

            returnValues.minutes = minutes;
            returnValues.seconds = seconds;

            if (seconds.toString().length === 1) {
                seconds = `0${seconds}`;
            }

            returnValues.string = `${minutes}:${seconds}`;

            return returnValues;
        },

        convertTimeToUrlFormat(time) {

            const returnValues = {};
            let incomingTime = time;

            const regex = /[0-9]?[0-9]:[0-9][0-9]/;
            const isValidFormat = regex.test(incomingTime);

            if (!isValidFormat) {
                incomingTime = '00:00';
            }

            const splittedTime = incomingTime.split(':');
            returnValues.minutes = Number(splittedTime[0]);
            returnValues.seconds = Number(splittedTime[1]);
            returnValues.string = returnValues.minutes * 60 + returnValues.seconds;

            return returnValues;
        },

        onInputVideoUrl(link) {
            this.element.config.videoID.value = this.shortenLink(link);
            this.$emit('element-update', this.element);
        },

        shortenLink(link) {
            let checkLink = link.match(/https:\/\/(:?www.)?(\w*)/)[2];
            if(checkLink === 'youtu' || checkLink === 'youtube') {
                this.element.config.videoType.value = 'youtube';
                let incomingLink = link;
                const shareLink = /https\:\/\/youtu\.be\//;
                const linkType = shareLink.test(incomingLink) ? 'shareLink' : 'urlLink';

                if (linkType === 'shareLink') {
                    const linkPrefix = /https\:\/\/youtu\.be\//;
                    const linkPostfix = /\?/;

                    incomingLink = incomingLink.replace(linkPrefix, '');

                    if (linkPostfix.test(incomingLink)) {
                        const positionOfPostfix = linkPostfix.exec(incomingLink).index;
                        incomingLink = incomingLink.substring(0, positionOfPostfix);
                    }
                } else {
                    const linkPrefix = /https\:\/\/www\.youtube\.com\/watch\?v\=/;
                    const linkPostfix = /\&/;

                    if (linkPrefix.test(incomingLink)) {
                        // removing the https://www...
                        incomingLink = incomingLink.replace(linkPrefix, '');
                    }

                    if (linkPostfix.test(incomingLink)) {

                        const positionOfPostfix = linkPostfix.exec(incomingLink).index;
                        incomingLink = incomingLink.substring(0, positionOfPostfix);
                    }
                }
                return incomingLink;
            }
            if(checkLink === 'vimeo') {
                this.element.config.videoType.value = 'vimeo';
                const videoLink = link;
                const videoIDPrefix = /https:\/\/vimeo\.com\//;
                const videoIDPostfix = /#/;
                let shortenLink = videoLink.replace(videoIDPrefix, '');

                if (videoIDPostfix.test(shortenLink)) {
                    const positionOfPostfix = videoIDPostfix.exec(shortenLink).index;
                    shortenLink = shortenLink.substring(0, positionOfPostfix);
                }

                return shortenLink;
            }
        },

        async onImageUpload({ targetId }) {
            const mediaEntity = await this.mediaRepository.get(targetId);

            this.element.config.previewMedia.value = mediaEntity.id;

            this.updateElementData(mediaEntity);

            this.$emit('element-update', this.element);
        },

        onImageRemove() {
            this.element.config.previewMedia.value = null;

            this.updateElementData();

            this.$emit('element-update', this.element);
        },

        onCloseModal() {
            this.mediaModalIsOpen = false;
        },

        onSelectionChanges(mediaEntity) {
            const media = mediaEntity[0];
            this.element.config.previewMedia.value = media.id;

            this.updateElementData(media);

            this.$emit('element-update', this.element);
        },

        updateElementData(media = null) {
            this.$set(this.element.data, 'previewMediaId', media === null ? null : media.id);
            this.$set(this.element.data, 'previewMedia', media);
        },

        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
        },

        //video text & description fields
        onBlur(content) {
            this.emitChanges(content);
        },

        onInput(content) {
            this.emitChanges(content);
        },

        onInputHeader(content) {
            if (content !== this.element.config.videoHeader.value) {
                this.element.config.videoHeader.value = content;
                this.$emit('element-update', this.element);
            }
        },

        emitChanges(content) {

            if (content !== this.element.config.content.value) {
                this.element.config.content.value = content;
                this.$emit('element-update', this.element);
            }
        },
    },
});
