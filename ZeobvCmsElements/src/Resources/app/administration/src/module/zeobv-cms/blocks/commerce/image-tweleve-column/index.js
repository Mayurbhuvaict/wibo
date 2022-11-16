import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'image-tweleve-column',
    label: 'sw-cms.blocks.image.imageTweleveColumn.label',
    category: 'commerce',
    component: 'sw-cms-block-image-tweleve-column',
    previewComponent: 'sw-cms-preview-image-tweleve-column',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        imageSlider: 'image-tweleve-column',
    },
});
