const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-cms-list', {
    computed: {
        listCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('previewMedia')
                // .addAssociation('products')
                .addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            if (this.term !== null) {
                criteria.setTerm(this.term);
            }

            if (this.currentPageType !== null) {
                criteria.addFilter(Criteria.equals('cms_page.type', this.currentPageType));
            }

            this.addLinkedLayoutsAggregation(criteria);

            return criteria;
        }
    }
});