import template from './zeobv-cms-preview-manufacturer-text.html.twig';
import './zeobv-cms-preview-manufacturer-text.scss';

Shopware.Component.register('zeobv-cms-preview-manufacturer-text', {
    template,

    computed: {
        demoCategoryElement() {
            return {
                config: {
                    customField: {
                        source: 'static',
                        value: null
                    },
                }
            };
        }
    },
});
