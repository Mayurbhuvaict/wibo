import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'text-image-text-uwaankooponzezorg',
    label: 'text-image-text-uw aan koop onze zorg',
    category: 'commerce',
    component: 'sw-cms-element-text-image-text-uwaankooponzezorg',
    configComponent: 'sw-cms-el-config-text-image-text-uwaankooponzezorg',
    previewComponent: 'sw-cms-el-preview-text-image-text-uwaankooponzezorg',
    defaultConfig: {
        text: {
            source: 'static',
            value: null
        },

    }

});
