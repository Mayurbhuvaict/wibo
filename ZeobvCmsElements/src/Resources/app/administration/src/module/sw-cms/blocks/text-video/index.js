import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'text-video',
    category: 'video',
    label: 'Wim Videos',
    component: 'sw-cms-block-text-video',
    previewComponent: 'sw-cms-preview-text-video',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        imageText: 'image-text',
        textVideo1: 'text-product-video',
        textVideo2: 'text-product-video'
    }
});
