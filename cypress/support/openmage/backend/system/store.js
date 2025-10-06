const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSystemStore = {
    config: {
        _id: '#nav-admin-system-store',
        _id_parent: '#nav-admin-system',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Manage Stores',
            url: 'system_store/index',
            __buttons: {
                addWebsite: defaultConfig._button + '[title="Create Website"]',
                addStore: defaultConfig._button + '[title="Create Store"]',
                addStoreView: defaultConfig._button + '[title="Create Store View"]',
            },
        },
    },
}
