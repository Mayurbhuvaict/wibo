import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'icon',
    label: 'sw-cms.elements.categorySlider.label',
    component: 'sw-cms-el-icon',
    configComponent: 'sw-cms-el-config-icon',
    previewComponent: 'sw-cms-el-preview-icon',
    defaultConfig: {
        media: {
            source: 'static',
            value: null
        },
        chatIcon: {
            source: 'static',
            value: null
        },
        callIcon: {
            source: 'static',
            value: null
        },
        mediaCall : {
            source: 'static',
            value: null
        },
        mediaCallUrl : {
            source: 'static',
            value: null
        },
        mediaUrl : {
            source: 'static',
            value: null
        }
    }
});
