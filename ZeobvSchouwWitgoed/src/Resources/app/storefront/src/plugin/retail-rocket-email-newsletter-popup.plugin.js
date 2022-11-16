import RetailRocketPlugin from '../../../../../../../ZeobvRetailRocket/src/Resources/app/storefront/src/retail-rocket/retail-rocket-plugin';

export default class RetailRocketEmailNewsletterPopup extends RetailRocketPlugin {
    registerEvents() {
        this.el.form.addEventListener('submit', this._onSubscribe.bind(this));
    }

    _onSubscribe() {
        const email = this.el.form.querySelector('input[name="email"]').value;

        (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(() => {
            rrApi.setEmail(email);
        });
    }
}
