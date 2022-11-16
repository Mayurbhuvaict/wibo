import template from './sw-cms-el-config-image-uwaankooponzezorg.html.twig';
import './sw-cms-el-config-image-uwaankooponzezorg.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-image-uwaankooponzezorg', {
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
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        uploadTag() {
            return `cms-element-media-config-${this.element.id}`;
        },

        previewSource() {
            if (this.element.data && this.element.data.media && this.element.data.media.id) {
                return this.element.data.media;
            }

            return this.element.config.media.value;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('image-uwaankooponzezorg');
        },

        displayText(value){
            this.element.config.displayText.value = value;
            this.$emit('element-update', this.element);
        },

        displayPrice(value){
            this.element.config.displayPrice.value = value;
            this.$emit('element-update', this.element);
        },

        displaySymbol(value) {
            this.element.config.displaySymbol.value = value;
            this.$emit('element-update', this.element);
        },

        async onImageUpload({ targetId }) {
            const mediaEntity = await this.mediaRepository.get(targetId);
            this.element.config.media.value = mediaEntity.id;
            this.element.config.url.value = mediaEntity.url;
            this.updateElementData(mediaEntity);

            this.$emit('element-update', this.element);
        },

        onImageRemove() {
            this.element.config.media.value = null;

            this.updateElementData();

            this.$emit('element-update', this.element);
        },

        onCloseModal() {
            this.mediaModalIsOpen = false;
        },

        onSelectionChanges(mediaEntity) {
            const media = mediaEntity[0];
            this.element.config.media.value = media.id;
            this.element.config.url.value = mediaEntity.url;
            this.updateElementData(media);

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

        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
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
