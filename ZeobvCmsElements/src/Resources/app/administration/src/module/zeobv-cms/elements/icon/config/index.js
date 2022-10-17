import template from './sw-cms-el-config-icon.html.twig';
import './sw-cms-el-config-icon.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-icon', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            mediaModalIsOpen: false,
            initialFolderId: null,
            mediaCallModalIsOpen: false,
        };
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        uploadTag() {
            return `cms-element-media-config-${this.element.id}`;
        },

        uploadCallTag() {
            return `cms-element-mediaCall-config-${this.element.id}`;
        },


        previewSource() {
            if (this.element.data && this.element.data.media && this.element.data.media.id) {
                return this.element.data.media;
            }
            return this.element.config.media.value;
        },

        previewSourceMediaCall(){
            if (this.element.data && this.element.data.mediaCall && this.element.data.mediaCall.id) {
                return this.element.data.mediaCall;
            }
            return this.element.config.mediaCall.value;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('icon');
        },
        displayContent(value) {
            this.element.config.chatIcon.value = value;
            this.element.config.url.value = media.id;
            this.$emit('element-update', this.element);
        },

        displayCallContent(value){
            this.element.config.callIcon.value = value;
            this.element.config.url.value = media.id;
            this.$emit('element-update', this.element);
        },
        async onImageUpload({ targetId }) {
            const mediaEntity = await this.mediaRepository.get(targetId);
            this.element.config.media.value = mediaEntity.id;
            this.element.config.mediaUrl.value = mediaEntity.url;
            this.updateElementData(mediaEntity);
            this.$emit('element-update', this.element);
        },

        async onImageMediaCallUpload({targetId}) {
            const mediaEntity = await this.mediaRepository.get(targetId);
            this.element.config.mediaCall.value = mediaEntity.id;
            this.element.config.mediaCallUrl.value = mediaEntity.url;
            this.updateElementMediaCallData(mediaEntity);
            this.$emit('element-update', this.element);
        },

        onImageRemove() {
            this.element.config.media.value = null;
            this.updateElementData();
            this.$emit('element-update', this.element);
        },

        onImageMediaCallRemove() {
            this.element.config.mediaCall.value = null;
            this.updateElementMediaCallData();
            this.$emit('element-update', this.element);
        },

        onCloseModal() {
            this.mediaModalIsOpen = false;
        },

        onCloseMediaCallModal() {
            this.mediaCallModalIsOpen = false;
        },

        onSelectionChanges(mediaEntity) {
            const media = mediaEntity[0];
            this.element.config.media.value = media.id;
            this.updateElementData(media);
            this.$emit('element-update', this.element);
        },

        onSelectionMediaCallChanges(mediaEntity) {
            const mediaCall = mediaEntity[0];
            this.element.config.mediaCall.value = mediaCall.id;
            this.updateElementMediaCallData(mediaCall);
            this.$emit('element-update', this.element);
        },

        updateElementData(media = null) {
            const mediaId = media === null ? null : media.id;
            if (!this.element.data) {
                this.$set(this.element, 'data', { mediaId });
                this.$set(this.element, 'data', { media });
            } else {
                this.$set(this.element.data, 'mediaId', mediaId);
                this.$set(this.element.data, 'media', media);
            }
        },

        updateElementMediaCallData(mediaCall = null) {
            const mediaId = mediaCall === null ? null : mediaCall.id;
            if (!this.element.data) {
                this.$set(this.element, 'data', { mediaId });
                this.$set(this.element, 'data', { mediaCall });
            } else {
                this.$set(this.element.data, 'mediaId', mediaId);
                this.$set(this.element.data, 'media', mediaCall);
            }
        },

        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
        },

        onOpenMediaCallModal() {
            this.mediaCallModalIsOpen = true;
        },

        onChangeMinHeight(value) {
            this.element.config.minHeight.value = value === null ? '' : value;
            this.$emit('element-update', this.element);
        },

        onChangeDisplayMode(value) {
            if (value === 'cover') {
                this.element.config.verticalAlign.value = null;
            }
            this.$emit('element-update', this.element);
        },
    },
});
