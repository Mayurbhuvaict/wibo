import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'text-read-more',
    label: 'sw-cms.elements.text-read-more.label',
    component: 'sw-cms-el-text-read-more',
    configComponent: 'sw-cms-el-config-text-read-more',
    previewComponent: 'sw-cms-el-preview-text-read-more',
    defaultConfig: {
        initialContent: {
            source: 'static',
            value: '<p><font color="#52667A">Lorem Ipsum dolor</font></p>'
        },
        expandedContent: {
            source: 'static',
            value: '<p><font color="#52667A">Lorem Ipsum dolor</font></p>'
        },
        expandButtonType: {
            source: 'static',
            value: 'light'
        },
        expandButtonLabel: {
            source: 'static',
            value: 'Read more'
        },
        collapseButtonType: {
            source: 'static',
            value: 'light'
        },
        collapseButtonLabel: {
            source: 'static',
            value: 'Collapse'
        }
    }
});
