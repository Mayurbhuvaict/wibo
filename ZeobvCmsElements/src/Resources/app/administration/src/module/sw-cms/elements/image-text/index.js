import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'image-text',
    label: 'sw-cms.elements.image-text.label',
    component: 'sw-cms-el-image-text',
    configComponent: 'sw-cms-el-config-image-text',
    previewComponent: 'sw-cms-el-preview-image-text',
    defaultConfig: {
        media: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'media',
            },
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
        content: {
            source: 'static',
            value: `
                <h3>Lorem Ipsum dolor sit amet</h3>
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                </p>
            `.trim(),
        },

    },
});
