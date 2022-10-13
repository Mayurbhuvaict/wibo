import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'manufacturer-text',
    label: 'zeobv-cms.blocks.product.manufacturerText.label',
    category: 'commerce',
    component: 'zeobv-cms-block-manufacturer-text',
    previewComponent: 'zeobv-cms-preview-manufacturer-text',
    defaultConfig: {
        customField: null
    },
    slots: {
        content: 'manufacturer-text'
    }
});
