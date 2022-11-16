import ZeobvQuotationCartPlugin from './plugin/zeobv-quotation-cart/zeobv-quotation-cart.plugin';

// Register via the existing PluginManager
const PluginManager = window.PluginManager;
PluginManager.override('AddToCart', ZeobvQuotationCartPlugin, '[data-add-to-cart]');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}
