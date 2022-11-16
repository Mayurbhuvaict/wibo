import './page/stock-import-list';

const { Module } = Shopware;

Module.register('stock-import', {
    type: 'plugin',
    name: 'stock-import.general.mainMenuItemGeneral',
    title: 'stock-import.general.mainMenuItemGeneral',
    description: 'stock-import.general.mainMenuItemGeneral',
    color: '#ff3d58',
    icon: 'default-action-cloud-download',

    routes: {
        list: {
            component: 'stock-import-list',
            path: 'list'
        },
    },

    navigation: [{
        id: 'stock-import-list',
        label: 'stock-import.general.mainMenuItemGeneral',
        parent: 'sw-catalogue',
        path: 'stock.import.list',
        position: 49,
        color: '#57d9a3',
    }],

    settingsItem: {
        group: 'plugins',
        to: 'stock.import.list',
        icon: 'default-text-code',
        backgroundEnabled: true,
    }

});
