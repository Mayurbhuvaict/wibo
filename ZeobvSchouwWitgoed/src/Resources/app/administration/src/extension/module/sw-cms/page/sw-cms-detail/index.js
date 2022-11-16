const { Component } = Shopware;
const Criteria = Shopware.Data.Criteria;

Component.override('sw-cms-detail', {
    computed: {
        loadPageCriteria() {
            const criteria = new Criteria(1, 1);
            const sortCriteria = Criteria.sort('position', 'ASC', true);

            criteria
                .addAssociation('categories')
                .addAssociation('landingPages')
                // .addAssociation('products.manufacturer')
                .getAssociation('sections')
                .addSorting(sortCriteria)
                .addAssociation('backgroundMedia')
                .getAssociation('blocks')
                .addSorting(sortCriteria)
                .addAssociation('backgroundMedia')
                .addAssociation('slots');

            return criteria;
        }
    }
});
