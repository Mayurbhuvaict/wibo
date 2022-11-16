const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-order-list', {
    methods: {
        async getList() {
            this.isLoading = true;

            const criteria = await Shopware.Service('filterService')
                .mergeWithStoredFilters(this.storeKey, this.orderCriteria);

            this.activeFilterNumber = criteria.filters.length;

            criteria.addAssociation('zeobvQuote');
            criteria.addFilter(
                Criteria.multi(
                    'OR',
                    [
                        Criteria.equals('order.extensions.zeobvQuote.id', null),
                        Criteria.equals('order.extensions.zeobvQuote.stateMachineState.technicalName', 'accepted'),
                    ]
                )
            );

            try {
                const response = await this.orderRepository.search(criteria);

                this.total = response.total;
                this.orders = response;
                this.isLoading = false;
            } catch {
                this.isLoading = false;
            }
        }
    }
});
