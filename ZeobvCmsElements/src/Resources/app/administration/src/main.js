// Base
import './module/sw-cms/base/fa-icon';
import './module/sw-cms/base/fa-icon-picker';

// Block
import './module/zeobv-cms/blocks/product/manufacturer-text';
import './module/zeobv-cms/blocks/commerce/product-banner';
import './module/zeobv-cms/blocks/commerce/text-category-slider';
import './module/zeobv-cms/blocks/commerce/text-image-text';
import './module/zeobv-cms/blocks/commerce/image-tweleve-column';
import './module/zeobv-cms/blocks/commerce/image-text-icon';
import './module/zeobv-cms/blocks/commerce/icon';
import './module/zeobv-cms/blocks/commerce/text-image-text-uwaankooponzezorg';
// Components
import './module/sw-cms/component/sw-cms-text-image-layout';

// Elements
import './module/sw-cms/elements/image-caption';
import './module/sw-cms/elements/product-slider';
import './module/sw-cms/elements/text-icon';
import './module/sw-cms/elements/text-read-more';
import './module/zeobv-cms/elements/banner-entry';
import './module/zeobv-cms/elements/manufacturer-text';
import './module/zeobv-cms/elements/product-banner-cta';
import './module/zeobv-cms/elements/text-category-slider';
import './module/zeobv-cms/elements/image-tweleve-column';
import './module/zeobv-cms/elements/icon';
import './module/zeobv-cms/elements/text-image-text-uwaankooponzezorg';
// Snippets (translations)
import swDeDE from './module/sw-cms/snippet/de-DE.json';
import swEnGB from './module/sw-cms/snippet/en-GB.json';
import zeoDeDE from './module/zeobv-cms/snippet/de-DE.json';
import zeoEnGB from './module/zeobv-cms/snippet/en-GB.json';

Shopware.Locale.extend('de-DE', swDeDE);
Shopware.Locale.extend('en-GB', swEnGB);

Shopware.Locale.extend('de-DE', zeoDeDE);
Shopware.Locale.extend('en-GB', zeoEnGB);

//extension
import './extension/sw-product/page/sw-product-detail';
import './extension/sw-product/view/sw-product-detail-description';
import './extension/sw-product/snippet/en-GB.json';
import './extension/sw-product/snippet/de-DE.json';

//registering tab at product detail page
Shopware.Module.register('sw-product-detail-tab-description', {

    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'sw.product.detail.description',
                path: '/sw/product/detail/:id/description',
                component: 'sw-product-detail-description',
                meta: {
                    parentPath: "sw.product.index"
                }
            });
        }
        next(currentRoute);
    }
});

