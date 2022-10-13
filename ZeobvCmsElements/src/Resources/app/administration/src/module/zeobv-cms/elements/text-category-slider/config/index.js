import template from './sw-cms-el-config-text-category-slider.html.twig';
import './sw-cms-el-config-text-category-slider.scss';

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register('sw-cms-el-config-text-category-slider', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            // We probably only need the categoryCollection for this plugin
            categoryCollection: null,
            categoryStream: null,
            showCategoryStreamPreview: false,

            // Temporary values to store the previous selection in case the user changes the assignment type.
            tempProductIds: [],
            tempStreamId: null
        };
    },


    computed: {

        // Category
        categoryRepository() {
            return this.repositoryFactory.create('category');
        },


        categories() {
            if (this.element.data && this.element.data.categories && this.element.data.categories.length > 0) {
                return this.element.data.categories;
            }

            return null;
        },


        categoryMediaFilter() {
            const criteria = new Criteria(1, 25);
            criteria.addAssociation('media');

            return criteria;
        },

        categoryMultiSelectContext() {
            const context = Object.assign({}, Shopware.Context.api);
            context.inheritance = true;

            return context;
        },
        // Change labels here
        categoryAssignmentTypes() {
            return [{
                label: this.$tc('sw-cms.elements.productSlider.config.productAssignmentTypeOptions.manual'),
                value: 'static'
            }, {
                label: this.$tc('sw-cms.elements.productSlider.config.productAssignmentTypeOptions.productStream'),
                value: 'category_stream'
            }];
        },
        categoryStreamSortingOptions() {
            return [{
                label: this.$tc('sw-cms.elements.productSlider.config.productStreamSortingOptions.nameAsc'),
                value: 'name:ASC'
            }, {
                label: this.$tc('sw-cms.elements.productSlider.config.productStreamSortingOptions.nameDesc'),
                value: 'name:DESC'
            }, {
                label: this.$tc('sw-cms.elements.productSlider.config.productStreamSortingOptions.priceAsc'),
                value: 'listingPrices:ASC'
            }, {
                label: this.$tc('sw-cms.elements.productSlider.config.productStreamSortingOptions.priceDesc'),
                value: 'listingPrices:DESC'
            }, {
                label: this.$tc('sw-cms.elements.productSlider.config.productStreamSortingOptions.creationDateAsc'),
                value: 'createdAt:ASC'
            }, {
                label: this.$tc('sw-cms.elements.productSlider.config.productStreamSortingOptions.creationDateDesc'),
                value: 'createdAt:DESC'
            }, {
                label: this.$tc('sw-cms.elements.productSlider.config.productStreamSortingOptions.random'),
                value: 'random'
            }];
        },

        categoryStreamCriteria() {
            const criteria = new Criteria(1, 10);
            const sorting = this.element.config.categoryStreamSorting.value;

            if (!sorting || sorting === 'random') {
                return criteria;
            }

            const field = sorting.split(':')[0];
            const direction = sorting.split(':')[1];

            criteria.addSorting(Criteria.sort(field, direction, false));

            return criteria;
        },

        categoryStreamPreviewColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-category.base.products.columnNameLabel'),
                    dataIndex: 'name',
                    sortable: false
                }, {
                    property: 'manufacturer.name',
                    label: this.$tc('sw-category.base.products.columnManufacturerLabel'),
                    sortable: false
                }
            ];
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('text-category-slider');
            this.categoryCollection = new EntityCollection('/category', 'category', Shopware.Context.api);

            if (this.element.config.categories.value.length <= 0) {
                return;
            }

            if (this.element.config.categories.source === 'category_stream') {
                this.loadCategoryStream();
            } else {
                // We have to fetch the assigned entities again
                // ToDo: Fix with NEXT-4830
                const criteria = new Criteria(1, 100);
                criteria.addAssociation('media');
                criteria.setIds(this.element.config.categories.value);

                this.categoryRepository
                    .search(criteria, Object.assign({}, Shopware.Context.api, { inheritance: true }))
                    .then((result) => {
                        this.categoryCollection = result;
                    });
            }
        },


        // Category
        onChangeAssignmentType(type) {
            if (type === 'category_stream') {
                this.tempProductIds = this.element.config.categories.value;
                this.element.config.categories.value = this.tempStreamId;
            } else {
                this.tempStreamId = this.element.config.categories.value;
                this.element.config.categories.value = this.tempProductIds;
            }
        },

        loadCategoryStream() {
            this.categoryStreamRepository
                .get(this.element.config.categories.value, Shopware.Context.api, new Criteria())
                .then((result) => {
                    this.categoryStream = result;
                });
        },

        onChangeCategoryStream(streamId) {
            if (streamId === null) {
                this.categoryStream = null;
                return;
            }

            this.loadCategoryStream();
        },

        onClickCategoryStreamPreview() {
            if (this.categoryStream === null) {
                return;
            }

            this.showCategoryStreamPreview = true;
        },

        onCloseCategoryStreamModal() {
            this.showCategoryStreamPreview = false;
        },

        onCategoriesChange() {
            this.element.config.categories.value = this.categoryCollection.getIds();
            this.$set(this.element.data, 'categories', this.categoryCollection);
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
    }
});
