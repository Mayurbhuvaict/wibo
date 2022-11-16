import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'product-banner',
    label: 'zeobv-cms.blocks.productBanner.imageText.label',
    category: 'commerce',
    component: 'sw-cms-block-product-banner',
    previewComponent: 'zeobv-cms-preview-product-banner',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
    },
    slots: {
        title: 'text',
        banner: {
            type: 'image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                    minHeight: { source: 'static', value: '300px' },
                },
                data: {
                    media: {
                        url: '/administration/static/img/cms/preview_camera_large.jpg',
                    },
                },
            },
        },
        entryOne: {
            type: 'banner-entry',
            default: {
                config: {
                    type: { source: 'static', value: 'category' },
                }
            },
        },
        entryTwo: {
            type: 'banner-entry',
            default: {
                config: {
                    type: { source: 'static', value: 'product' },
                }
            },
        },
        entryThree: {
            type: 'banner-entry',
            default: {
                config: {
                    type: { source: 'static', value: 'product' },
                }
            },
        },
        ctaButton: {
            type: 'product-banner-cta',
            default: {
                config: {
                    type: { source: 'static', value: 'button' },
                    label: { source: 'static', value: 'Click here' },
                }
            },
        },
        ctaLink: {
            type: 'product-banner-cta',
            default: {
                config: {
                    type: { source: 'static', value: 'link' },
                    label: { source: 'static', value: 'Vraag het onze Anne' },
                }
            },
        },
    },
});
