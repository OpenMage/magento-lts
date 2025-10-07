const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.store;

/**
 * Configuration for "System > Manage Stores" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-store',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_store/index',
}

/**
 * Configuration for "Manage Stores" page
 * @type {{__buttons: {addStoreView: string, addWebsite: string, addStore: string}, title: string, url: string}}
 */
test.config.index = {
    title: 'Manage Stores',
    url: test.config.url,
    __buttons: {
        addWebsite: base._button + '[title="Create Website"]',
        addStore: base._button + '[title="Create Store"]',
        addStoreView: base._button + '[title="Create Store View"]',
    },
}
