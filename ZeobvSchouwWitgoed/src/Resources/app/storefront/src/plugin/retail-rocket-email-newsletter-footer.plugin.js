import RetailRocketPlugin from '../../../../../../../ZeobvRetailRocket/src/Resources/app/storefront/src/retail-rocket/retail-rocket-plugin';

export default class RetailRocketEmailNewsletterFooter extends RetailRocketPlugin {
    registerEvents() {
        const FormValidationPlugin = window.PluginManager.getPluginInstanceFromElement(this.el.form, 'FormValidation');
        FormValidationPlugin.$emitter.subscribe('beforeSubmit', this._onSubscribe.bind(this));
    }

    _onSubscribe() {
        const email = this.el.form.querySelector('input[name="email"]').value;

        (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(() => {
            rrApi.setEmail(email);
        });
    }
}
