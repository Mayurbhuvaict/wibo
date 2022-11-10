import template from './sw-product-detail.html.twig';

const {Criteria} = Shopware.Data;

Shopware.Component.override('sw-product-detail', {
    template,

    computed: {
        productCriteria() {
            const criteria = this.$super('productCriteria');
            criteria.getAssociation('pageProduct');
            return criteria;
        },
    },
});
