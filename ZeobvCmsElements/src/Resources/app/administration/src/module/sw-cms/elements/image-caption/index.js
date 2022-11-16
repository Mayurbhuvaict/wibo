import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'image-caption',
    label: 'sw-cms.elements.image-caption.label',
    component: 'sw-cms-el-image-caption',
    configComponent: 'sw-cms-el-config-image-caption',
    previewComponent: 'sw-cms-el-preview-image-caption',
    defaultConfig: {
        media: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'media'
            }
        },
        displayMode: {
            source: 'static',
            value: 'standard'
        },
        url: {
            source: 'static',
            value: null
        },
        newTab: {
            source: 'static',
            value: false
        },
        minHeight: {
            source: 'static',
            value: '340px'
        },
        verticalAlign: {
            source: 'static',
            value: null
        },
        caption: {
            source: 'static',
            value: `<p style="color: #52667A;">Lorem Ipsum dolor</p>`
        },
        captionPosition: {
            source: 'static',
            value: 'bottom'
        },
        captionBackgroundColor: {
            source: 'static',
            value: `#D1D9E0`
        },
    }
});
