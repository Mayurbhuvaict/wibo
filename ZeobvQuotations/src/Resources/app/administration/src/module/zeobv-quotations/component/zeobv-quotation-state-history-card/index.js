const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('zeobv-quotation-state-history-card', 'sw-order-state-history-card', {
    data: {
        quotation: null
    },

    computed: {
        stateMachineHistoryCriteria() {
            const criteria = new Criteria(1, 50);

            const entityIds = [this.order.id];

            if (this.transaction) {
                entityIds.push(this.transaction.id);
            }

            if (this.delivery) {
                entityIds.push(this.delivery.id);
            }

            criteria.addFilter(
                Criteria.equalsAny(
                    'state_machine_history.entityId.id',
                    entityIds
                )
            );
            criteria.addFilter(
                Criteria.equalsAny(
                    'state_machine_history.entityName',
                    ['zeobv_quote']
                )
            );
            criteria.addAssociation('fromStateMachineState');
            criteria.addAssociation('toStateMachineState');
            criteria.addAssociation('user');
            criteria.addSorting({ field: 'state_machine_history.createdAt', order: 'ASC' });

            return criteria;
        },

        stateMachineStateCriteria() {
            const criteria = new Criteria();
            criteria.addSorting({ field: 'name', order: 'ASC' });
            criteria.addAssociation('stateMachine');
            criteria.addFilter(
                Criteria.equalsAny(
                    'state_machine_state.stateMachine.technicalName',
                    ['quotation.state']
                )
            );

            return criteria;
        }
    },

    methods: {
        getStateHistoryEntries() {
            // @ToDo: make sure the quotation status history is being loaded
            return this.$super('getStateHistoryEntries').then((fetchedEntries) => {
                this.quotationHistory = this.$super('buildStateHistory', this.order.extensions.zeobvQuote, fetchedEntries);

                return Promise.resolve(fetchedEntries);
            });
        }
    }
});
