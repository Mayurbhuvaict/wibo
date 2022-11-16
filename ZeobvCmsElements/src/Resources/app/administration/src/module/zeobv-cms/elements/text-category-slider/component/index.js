import template from './sw-cms-el-text-category-slider.html.twig';
import './sw-cms-el-text-category-slider.scss';

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register('sw-cms-el-text-category-slider', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            sliderBoxLimit: 3,
            categoryCollection: null,
            context: Shopware.Context.api,
        };
    },

    computed: {
        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        hasNavigation() {
            return !!this.element.config.navigation.value;
        },

        classes() {
            return {
                'has--navigation': this.hasNavigation,
                'has--border': !!this.element.config.border.value
            };
        },

        sliderBoxMinWidth() {
            if (this.element.config.elMinWidth.value && this.element.config.elMinWidth.value.indexOf('px') > -1) {
                return `repeat(auto-fit, minmax(${this.element.config.elMinWidth.value}, 1fr))`;
            }

            return null;
        },

        currentDeviceView() {
            return this.cmsPageState.currentCmsDeviceView;
        },

    },

    watch: {
        'element.config.elMinWidth.value': {
            handler() {
                this.setSliderRowLimit();
            }
        },

        currentDeviceView() {
            setTimeout(() => {
                this.setSliderRowLimit();
            }, 400);
        }
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.mountedComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('category-slider');


            this.categoryCollection = new EntityCollection('/category', 'category', this.context);
            if (this.element.config.categories.value.length > 0) {
                const criteria = new Criteria(1, 100);
                criteria.addAssociation('media');
                criteria.setIds(this.element.config.categories.value);
                this.categoryRepository.search(criteria, Object.assign({}, this.context, {
                    inheritance: true,
                })).then(result => {
                    this.categoryCollection = result;
                    this.updateCategoriesDataValue();
                });
            }
        },

        mountedComponent() {
            this.setSliderRowLimit();
        },
        updateCategoriesDataValue() {
            if (this.element.config.categories.value) {
                const categories = [];
                this.categoryCollection.forEach((category) => {
                    categories.push(category);
                });
                this.$set(this.element.data, 'categories', categories);
            }
        },

        setSliderRowLimit() {
            if (this.currentDeviceView === 'mobile' || this.$refs.categoryHolder.offsetWidth < 500) {
                this.sliderBoxLimit = 1;
                return;
            }

            if (!this.element.config.elMinWidth.value ||
                this.element.config.elMinWidth.value === 'px' ||
                this.element.config.elMinWidth.value.indexOf('px') === -1) {
                this.sliderBoxLimit = 3;
                return;
            }

            if (parseInt(this.element.config.elMinWidth.value.replace('px', ''), 10) <= 0) {
                return;
            }

            // Subtract to fake look in storefront which has more width
            const fakeLookWidth = 100;
            const boxWidth = this.$refs.categoryHolder.offsetWidth;
            const elGap = 32;
            let elWidth = parseInt(this.element.config.elMinWidth.value.replace('px', ''), 10);

            if (elWidth >= 300) {
                elWidth -= fakeLookWidth;
            }

            this.sliderBoxLimit = Math.floor(boxWidth / (elWidth + elGap));
        },

    }
});
