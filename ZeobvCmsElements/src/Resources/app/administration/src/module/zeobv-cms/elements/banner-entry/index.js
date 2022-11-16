import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'banner-entry',
    label: 'zeobv-cms.elements.productBanner.bannerEntry.label',
    component: 'zeobv-cms-el-product-banner-banner-entry',
    configComponent: 'zeobv-cms-el-config-product-banner-banner-entry',
    previewComponent: 'zeobv-cms-el-preview-product-banner-banner-entry',
    defaultConfig: {
        type: {
            source: 'static',
            value: 'category',
        },
        id: {
            source: 'static',
            value: '',
        },
        navigation: {
            source: 'static',
            value: true,
        }
    },
});
