import './component';
import './config';
import './preview';

const Criteria = Shopware.Data.Criteria;
const criteria = new Criteria();
criteria.addAssociation('media');

Shopware.Service('cmsService').registerCmsElement({
    name: 'text-category-slider',
    label: 'sw-cms.elements.categorySlider.label',
    component: 'sw-cms-el-text-category-slider',
    configComponent: 'sw-cms-el-text-category-slider',
    previewComponent: 'sw-cms-el-preview-text-category-slider',
    defaultConfig: {
        content: {
            source: 'static',
            value: `
                <h2>Lorem Ipsum dolor sit amet</h2>
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
                Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
                At vero eos et accusam et justo duo dolores et ea rebum.
                Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
            `.trim(),
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
        categories: {
            source: 'mapped',
            value: [],
            required: true,
            entity: {
                name: 'categories',
                criteria: criteria
            }
        },
        title: {
            source: 'static',
            value: ''
        },
        navigation: {
            source: 'static',
            value: true
        },
        rotate: {
            source: 'static',
            value: false
        },
        border: {
            source: 'static',
            value: false
        },
        elMinWidth: {
            source: 'static',
            value: '250px'
        },
        arrowColor: {
            source: 'static',
            value: '#bf0d10'
        },
        arrowBackgroundColor: {
            source: 'static',
            value: '#fafafa',
        },
        categoryBannerBackgroundColor: {
            source: 'static',
            value: '#ffffff',
        },
        categoryBannerTextColor: {
            source: 'static',
            value: '#000000',
        },
        borderHoverColor: {
            source: 'static',
            value: '#7c0002'
        },
        categoryBannerArrowColor: {
            source: 'static',
            value: '#bf0d10',
        },
        categoryStreamSorting: {
            source: 'static',
            value: 'name:ASC'
        },
        categoryStreamLimit: {
            source: 'static',
            value: 10
        }
    },
    collect: function collect(elem) {
        const context = Object.assign(
            {},
            Shopware.Context.api,
            { inheritance: true }
        );

        const criteriaList = {};

        Object.keys(elem.config).forEach((configKey) => {
            if (elem.config[configKey].source === 'mapped') {
                return;
            }

            // Category if-statement
            if (elem.config[configKey].source === 'category_stream') {
                return;
            }

            const entity = elem.config[configKey].entity;

            if (entity && elem.config[configKey].value) {
                const entityKey = entity.name;
                const entityData = {
                    value: [...elem.config[configKey].value],
                    key: configKey,
                    searchCriteria: entity.criteria ? entity.criteria : new Criteria(),
                    ...entity
                };

                entityData.searchCriteria.setIds(entityData.value);
                entityData.context = context;

                criteriaList[`entity-${entityKey}`] = entityData;
            }
        });

        return criteriaList;
    }
});
