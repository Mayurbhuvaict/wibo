import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'product-banner-cta',
    label: 'zeobv-cms.elements.productBanner.cta.label',
    component: 'zeobv-cms-el-product-banner-cta',
    configComponent: 'zeobv-cms-el-config-product-banner-cta',
    previewComponent: 'zeobv-cms-el-preview-product-banner-cta',
    defaultConfig: {
        type: {
            source: 'static',
            value: 'button',
        },
        label: {
            source: 'static',
            value: '',
        },
        targetUrl: {
            source: 'static',
            value: '',
        },
    },
});
