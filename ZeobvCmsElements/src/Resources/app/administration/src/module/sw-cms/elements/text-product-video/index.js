import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'text-product-video',
    label: 'sw-cms.elements.image-text.label',
    component: 'sw-cms-el-text-product-video',
    configComponent: 'sw-cms-el-config-text-product-video',
    previewComponent: 'sw-cms-el-preview-text-product-video',
    defaultConfig: {
        videoID: {
            source: 'static',
            value: '',
            required: true,
        },
        autoPlay: {
            source: 'static',
            value: false,
        },
        loop: {
            source: 'static',
            value: false,
        },
        showControls: {
            source: 'static',
            value: true,
        },
        advancedPrivacyMode: {
            source: 'static',
            value: true,
        },
        needsConfirmation: {
            source: 'static',
            value: false,
        },
        previewMedia: {
            source: 'static',
            value: null,
            entity: {
                name: 'media',
            },
        },
        content: {
            source: 'static',
            required: true,
            value: `
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                </p>
            `.trim(),
        },
        videoHeader:{
            source: 'static',
            value: 'Lorem Ipsum dolor sit amet',
            required: true
        },
        videoType: {
            source: 'static',
            value: ''
        }
    },
});
