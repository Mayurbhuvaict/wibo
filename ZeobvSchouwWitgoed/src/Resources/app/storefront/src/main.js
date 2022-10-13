import SlideUsps from './plugin/slide-usps.plugin';
import StickyFooterBuyButtonPlugin from './plugin/sticky-add-to-cart.plugin';
import TabPaneCollapse from './plugin/tab-pane-collapse.plugin';
import Tooltip from './plugin/tooltip.plugin';
import CookieAccept from './plugin/cookie-accept.plugin';
import PrintButtonExtraPlugin from './plugin/print-button-extra.plugin';
import OpenFiltersPlugin from './plugin/open-filters.plugin';
import ProductSliderPluginOverride from './plugin/extension/slider/product-slider.plugin.override';
import OpenTrengoButtonPlugin from './plugin/open-trengo-button.plugin';
import RetailRocketEmailNewsletterFooterPlugin from "./plugin/retail-rocket-email-newsletter-footer.plugin";
import RetailRocketEmailNewsletterPopupPlugin from "./plugin/retail-rocket-email-newsletter-popup.plugin";

const PluginManager = window.PluginManager;

// Register deps to Pluginmanager
PluginManager.register('SlideUsps', SlideUsps, '.cms-section.usp-wrapper', {
    // showOnScrollPosition: 150,
});

PluginManager.override('ProductSlider', ProductSliderPluginOverride, '[data-product-slider]');

// sticky add to cart on PDP
PluginManager.register('StickyFooterBuyButton', StickyFooterBuyButtonPlugin, '.nav-tabs-wrapper');

PluginManager.register('TabPaneCollapse', TabPaneCollapse, '[data-collapsable-tab]');

PluginManager.register('CookieAccept', CookieAccept);

// add print button extra functionality pdp
PluginManager.register('PrintButtonExtra', PrintButtonExtraPlugin, '[data-print-button]');

// Pre-open 5 filters on category
PluginManager.register('OpenFilters', OpenFiltersPlugin, '#filter-panel-wrapper');

// Open the Trengo widget on click.
PluginManager.register('OpenTrengoButtonPlugin', OpenTrengoButtonPlugin, '.open-trengo-button');

// Filter tooltip.
PluginManager.register('Tooltip', Tooltip, '[data-schouw-tooltip]');

// Theme specific Retail Rocket trackers
PluginManager.register('RetailRocketEmailNewsletterFooter', RetailRocketEmailNewsletterFooterPlugin, '[data-retail-rocket-email-newsletter-footer]');
PluginManager.register('RetailRocketEmailNewsletterPopup', RetailRocketEmailNewsletterPopupPlugin, '[data-retail-rocket-email-newsletter-popup]');
