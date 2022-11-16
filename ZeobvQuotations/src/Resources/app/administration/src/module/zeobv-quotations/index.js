import './page/zeobv-quotations-list';
import './page/zeobv-quotations-detail';

import './view/zeobv-quotations-detail-base';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';
import nlNL from './snippet/nl-NL.json';

const { Module } = Shopware;

Module.register('zeobv-quotations', {
    type: 'plugin',
    name: 'Quotations',
    title: 'zeobv-quotations.general.mainMenuItemGeneral',
    description: 'sw-property.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'default-shopping-paper-bag-product',

    snippets: {
        'de-DE': deDE,
        'nl-NL': nlNL,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'zeobv-quotations-list',
            path: 'list'
        },
        detail: {
            component: 'zeobv-quotations-detail',
            path: 'detail/:id',
            redirect: {
                name: 'zeobv.quotations.detail.base'
            },
            meta: {
                privilege: 'order.viewer',
                appSystem: {
                    view: 'detail'
                }
            },
            children: {
                base: {
                    component: 'zeobv-quotations-detail-base',
                    path: 'base',
                    meta: {
                        parentPath: 'zeobv.quotations.detail',
                        privilege: 'order.viewer'
                    }
                }
            },
            props: {
                default: ($route) => {
                    return { orderId: $route.params.id };
                }
            }
        }
    },

    navigation: [{
        label: 'zeobv-quotations.general.mainMenuItemGeneral',
        parent: 'sw-order',
        color: '#ff3d58',
        path: 'zeobv.quotations.list',
        icon: 'default-shopping-paper-bag-product',
        position: 100
    }]
});
