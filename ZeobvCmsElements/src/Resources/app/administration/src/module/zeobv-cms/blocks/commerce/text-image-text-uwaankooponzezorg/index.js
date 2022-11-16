import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'text-image-text-uwaankooponzezorg',
    label: 'text-image-text-uw aan koop onze zorg',
    category: 'commerce',
    component: 'sw-cms-block-text-image-text-uwaankooponzezorg',
    previewComponent: 'sw-cms-preview-text-image-text-uwaankooponzezorg',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        left: 'text',
        center: {
            type: 'image',
            default: {
                config: {
                    displayMode: {source: 'static', value: 'standard'},
                },
                data: {
                    media: {
                        source: 'default',
                    },
                },
            },
        },
        right: 'text'
    }
});
