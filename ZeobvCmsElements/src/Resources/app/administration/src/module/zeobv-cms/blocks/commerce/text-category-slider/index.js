import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'text-category-slider',
    label: 'zeobv-cms.blocks.textCategorySlider.label',
    category: 'commerce',
    component: 'sw-cms-block-text-category-slider',
    previewComponent: 'sw-cms-preview-text-category-slider',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        left: 'text',
        right: 'category-slider'
    }
});
