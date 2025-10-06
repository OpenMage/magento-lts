const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemStore = {
    config: {
        _id: '#nav-admin-system-store',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Stores',
            url: 'system_store/index',
            __buttons: {
                addWebsite: '.form-buttons button[title="Create Website"]',
                addStore: '.form-buttons button[title="Create Store"]',
                addStoreView: '.form-buttons button[title="Create Store View"]',
            },
        },
    },
}
