const base = {
    _button: '.form-buttons button',
}

cy.testBackendSystemStore = {};

cy.testBackendSystemStore.config = {
    _id: '#nav-admin-system-store',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendSystemStore.config.index = {
    title: 'Manage Stores',
    url: 'system_store/index',
    __buttons: {
        addWebsite: base._button + '[title="Create Website"]',
        addStore: base._button + '[title="Create Store"]',
        addStoreView: base._button + '[title="Create Store View"]',
    },
}
