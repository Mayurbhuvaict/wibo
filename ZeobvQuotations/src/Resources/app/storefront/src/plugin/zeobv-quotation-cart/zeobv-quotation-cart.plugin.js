import AddToCartPlugin from 'src/plugin/add-to-cart/add-to-cart.plugin';

export default class ZeobvQuotationCartPlugin extends AddToCartPlugin {
    submitQuotation = false;

    _registerEvents() {
        super._registerEvents();

        this.$emitter.subscribe('beforeFormSubmit', (event) => {
            this.addQuotationKeyToForm(event.detail);
        });
    }

    _formSubmit(event) {
        if (event.submitter.name === 'zeobvAddToQuotation') {
            this.submitQuotation = true;
        }

        super._formSubmit(event);
    }

    addQuotationKeyToForm(formData) {
        if (this.submitQuotation) {
            formData.append('zeobvAddToQuotation', true);
        }

        this.submitQuotation = false;
    }
}
