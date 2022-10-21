import template from './sw-product-detail-description.html.twig';

const {EntityCollection, Criteria} = Shopware.Data;
const {Component} = Shopware;
const {mapState, mapGetters} = Shopware.Component.getComponentHelper();

Component.register('sw-product-detail-description', {
    template,

    inject: ['repositoryFactory'],

    data() {
        return {
            showMediaModal: false,
            test: null,
            productdatas: null,
            // isLoadingData: false,
            mediaId: null,
        };
    },

    metaInfo() {
        return {
            title: 'product_page'
        };
    },

    computed: {
        ...mapState('swProductDetail', [
            'product'
        ]),

        ...mapGetters('swProductDetail', [
            'isLoading'
        ]),

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        productPageRepository() {
            return this.repositoryFactory.create('product_page');
        },
        productRepository() {
            return this.repositoryFactory.create('product');
        },

        uploadTag() {
            return `product-description-detail--${this.product.id}`;
        },

        // isLoadingGrid() {
        //     return this.isLoadingData || this.isLoading;
        // },

        mediaItem() {
            return this.product.extensions.pageProduct !== null ? this.product.extensions.pageProduct.mediaId : null;
        },

    },

    created() {
        this.createdComponent();
    },

    watch: {
        'product.extensions.pageProduct.mediaId'() {

            if (!this.product.extensions.pageProduct.mediaId) {
                return;
            }
            this.setMediaItem({targetId: this.product.extensions.pageProduct.mediaId});
        },
    },
    methods: {

        onUploadMedia(media) {
            this.$emit('media-upload', {targetId: media.targetId});
        },

        onRemoveMedia() {
            this.$emit('media-remove');
        },

        onOpenMedia() {
            this.$emit('media-open');
        },

        setMediaItem({targetId}) {

            this.mediaRepository.get(targetId).then((response) => {
                this.mediaId = response;

            });
            this.product.extensions.pageProduct.mediaId = targetId;

        },

        onDropMedia(mediaItem) {
            this.setMediaItem({targetId: mediaItem.id});
        },

        openMediaModal() {
            this.showMediaModal = true;
        },


        setMediaFromSidebar(media) {
            this.product.extensions.pageProduct.mediaId = media.id;
        },

        onUnlinkLogo() {
            this.mediaId = null;

        },

        onOpenMediaModal() {
            this.showMediaModal = true;
        },

        onCloseMediaModal() {
            this.showMediaModal = false;
        },

        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        },

        createdComponent() {
            if (!(this.product.extensions && this.product.extensions.pageProduct)) {
                const newBundle = this.productPageRepository.create(Shopware.Context.api);
                this.$set(this.product.extensions, 'pageProduct', newBundle);
                console.log(this.product.extensions);
            }
        }
    }
});
