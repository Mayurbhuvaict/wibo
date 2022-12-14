import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'image-uwaankooponzezorg',
    label: 'image-uwaankooponzezorg',
    category: 'commerce',
    component: 'sw-cms-block-image-uwaankooponzezorg',
    previewComponent: 'sw-cms-preview-image-uwaankooponzezorg',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        image: {
            type: 'image-uwaankooponzezorg',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'standard' },
                },
                data: {
                    media: {
                        url: '/administration/static/img/cms/preview_mountain_large.jpg',
                    },
                },
            }
        }
    }
});
