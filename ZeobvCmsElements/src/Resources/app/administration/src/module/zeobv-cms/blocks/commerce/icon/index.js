import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'icon',
    label: 'icon',
    category: 'image',
    component: 'sw-cms-block-icon',
    previewComponent: 'sw-cms-preview-icon',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        image: {
            type: 'icon',
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
