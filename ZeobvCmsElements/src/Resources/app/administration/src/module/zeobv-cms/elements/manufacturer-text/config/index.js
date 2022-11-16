import template from './zeobv-cms-el-config-manufacturer-text.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('zeobv-cms-el-config-manufacturer-text', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    computed: {
        customFieldCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('customFieldSet.relations.entityName', 'product_manufacturer'));
            return criteria;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('manufacturer-text');
        },

        onElementUpdate(element) {
            this.$emit('element-update', element);
        }
    }
});
