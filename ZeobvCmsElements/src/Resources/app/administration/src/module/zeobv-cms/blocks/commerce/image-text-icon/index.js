import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'image-text-icon',
    label: 'zeobv-cms.blocks.textImage.imageTextIcon.label',
    category: 'commerce',
    component: 'sw-cms-block-image-text-icon',
    previewComponent: 'sw-cms-preview-image-text-icon',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        left: 'image',
        center: 'text',
        right: 'icon',
    },
});
