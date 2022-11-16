import template from './zeobv-quotations-detail-base.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('zeobv-quotations-detail-base', 'sw-order-detail-base', {
    template,

    inject: [
        'stateMachineService',
        'repositoryFactory',
        'orderStateMachineService',
    ],

    data() {
        return {
            quotationOptions: [],
            showModal: false,
        }
    },

    computed: {
        orderCriteria() {
            const criteria = this.$super('orderCriteria', ...arguments);

            criteria.addAssociation('zeobvQuote');

            return criteria;
        },

        stateMachineStateCriteria() {
            const criteria = new Criteria();

            criteria.addSorting({ field: 'name', order: 'ASC' });
            criteria.addAssociation('stateMachine');
            criteria.addFilter(
                Criteria.equalsAny(
                    'state_machine_state.stateMachine.technicalName',
                    ['quotation.state'],
                ),
            );

            return criteria;
        },

        quotationOptionPlaceholder() {
            if (this.isLoading) return null;

            return `${this.$tc('zeobv-quotations.detail.stateCard.headlineQuotationState')}: \
            ${this.order.extensions.zeobvQuote.stateMachineState.translated.name}`;
        },
    },

    methods: {
        createdComponent() {
            this.$super('createdComponent', ...arguments);
            this.stateMachineStateRepository = this.repositoryFactory.create('state_machine_state');
        },

        reloadEntityData() {
            this.$super('reloadEntityData', ...arguments).then(() => {
                this.getTransitionOptions();
            });
        },

        loadQuotationStates() {
            return this.stateMachineService.getState('zeobv_quote', this.order.extensions.zeobvQuote.id,{},{},'quotationStateId')
        },

        getTransitionOptions() {
            const statePromises = [this.loadQuotationStates()];

            return Promise.all(
                [
                    this.getAllStates(),
                    ...statePromises,
                ],
            ).then((data) => {
                const allStates = data[0];
                const quotationState = data[1];
                this.quotationOptions = this.buildTransitionOptions(
                    'order.quotation_state',
                    allStates,
                    quotationState.data.transitions,
                );

                return Promise.resolve();
            });
        },

        buildTransitionOptions(stateMachineName, allTransitions, possibleTransitions) {
            const options = allTransitions.map((state, index) => {
                const option = {
                    stateName: state.technicalName,
                    id: null,
                    name: state.translated.name,
                    disabled: true,
                };

                if (this.feature.isActive('FEATURE_NEXT_7530')) {
                    option.id = index;
                }

                return option;
            });

            options.forEach((option) => {
                const transitionToState = possibleTransitions.filter((transition) => {
                    return transition.toStateName === option.stateName;
                });
                if (transitionToState.length >= 1) {
                    option.disabled = false;
                    option.id = transitionToState[0].actionName;
                }

                return option;
            });

            return options;
        },

        getAllStates() {
            return this.stateMachineStateRepository.search(this.stateMachineStateCriteria);
        },

        onQuickQuotationStatusChange(actionName) {
            this.orderStateMachineService.transitionQuotationState(
                this.order.extensions.zeobvQuote.id,
                actionName,
            ).then(() => {
                this.$emit('order-state-change');
            });
        },
    }
});
