import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'manufacturer-text',
    label: 'zeobv-cms.elements.manufacturer-text.label',
    component: 'zeobv-cms-el-manufacturer-text',
    configComponent: 'zeobv-cms-el-config-manufacturer-text',
    previewComponent: 'zeobv-cms-el-preview-manufacturer-text',
    defaultConfig: {
        customField: {
            source: 'static',
            value: null
        },
    }
});
