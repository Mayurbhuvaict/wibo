import template from './zeobv-quotations-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('zeobv-quotations-list', 'sw-order-list', {
    template,

    methods: {
        async getList() {
            this.isLoading = true;

            const criteria = await Shopware.Service('filterService')
                .mergeWithStoredFilters(this.storeKey, this.orderCriteria);

            this.activeFilterNumber = criteria.filters.length;

            criteria.addAssociation('zeobvQuote.stateMachineState');
            criteria.addFilter(Criteria.not('and', [Criteria.equals('order.zeobvQuote.id', null)]));

            try {
                const response = await this.orderRepository.search(criteria);

                this.total = response.total;
                this.orders = response;
                this.isLoading = false;
            } catch {
                this.isLoading = false;
            }
        },

        getOrderColumns() {
            return [{
                property: 'extensions.zeobvQuote.quoteNumber',
                label: 'zeobv-quotations.list.columnQuoteNumber',
                routerLink: 'zeo.quotations.detail',
                allowResize: true,
                primary: true
            }, {
                property: 'salesChannel.name',
                label: 'sw-order.list.columnSalesChannel',
                allowResize: true
            }, {
                property: 'orderCustomer.firstName',
                dataIndex: 'orderCustomer.lastName,orderCustomer.firstName',
                label: 'sw-order.list.columnCustomerName',
                allowResize: true
            },  {
                property: 'extensions.zeobvQuote.stateMachineState.name',
                label: 'zeobv-quotations.list.columnState',
                allowResize: true
            }, {
                property: 'extensions.zeobvQuote.expiryDate',
                label: 'zeobv-quotations.list.columnExpiryDate',
                allowResize: true
            }];
        },

        getVariantFromQuotationState(order) {
            return this.stateStyleDataProviderService.getStyle(
                'quotation.state', order.extensions.zeobvQuote.stateMachineState.technicalName
            ).variant;
        }
    }
});
