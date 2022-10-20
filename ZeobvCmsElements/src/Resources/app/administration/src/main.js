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

// Snippets (translations)
import swDeDE from './module/sw-cms/snippet/de-DE.json';
import swEnGB from './module/sw-cms/snippet/en-GB.json';
import zeoDeDE from './module/zeobv-cms/snippet/de-DE.json';
import zeoEnGB from './module/zeobv-cms/snippet/en-GB.json';

Shopware.Locale.extend('de-DE', swDeDE);
Shopware.Locale.extend('en-GB', swEnGB);

Shopware.Locale.extend('de-DE', zeoDeDE);
Shopware.Locale.extend('en-GB', zeoEnGB);
