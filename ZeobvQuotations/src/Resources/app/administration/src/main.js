import './module/zeobv-quotations';
import './override/sw-order';

// add state machine transition handler for quotation state
Shopware.Application.$container.resetProviders();
Shopware.Application.addServiceProviderDecorator('orderStateMachineService', (orderStateMachineService) => {
    orderStateMachineService.transitionQuotationState = (quotationId, actionName) => {
        const route = `_action/zeobv_quote/${quotationId}/state/${actionName}`;

        const headers = orderStateMachineService.getBasicHeaders();

        return orderStateMachineService.httpClient
            .post(route, {}, {
                headers,
            });
    };

    return orderStateMachineService;
});
