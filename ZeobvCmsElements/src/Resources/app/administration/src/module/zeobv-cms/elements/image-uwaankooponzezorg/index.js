import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'image-uwaankooponzezorg',
    label: 'sw-cms.elements.image.label',
    component: 'sw-cms-el-image-uwaankooponzezorg',
    configComponent: 'sw-cms-el-config-image-uwaankooponzezorg',
    previewComponent: 'sw-cms-el-preview-image-uwaankooponzezorg',
    defaultConfig: {
        media: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'media',
            },
        },
        displayText: {
            source: 'static',
            value: null
        },
        displayPrice: {
            source: 'static',
            value: null
        },
        displaySymbol: {
            source: 'static',
            value: null
        },
        displayMode: {
            source: 'static',
            value: 'standard',
        },
        url: {
            source: 'static',
            value: null,
        },
        newTab: {
            source: 'static',
            value: false,
        },
        minHeight: {
            source: 'static',
            value: '340px',
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
    },
});
