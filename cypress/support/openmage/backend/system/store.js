const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.store;

/**
 * Configuration for "System > Manage Stores" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-system-store',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_store/index',
    index: {},
}

/**
 * Configuration for "Manage Stores" page
 * @type {{__buttons: {addStoreView: string, addWebsite: string, addStore: string}, title: string, url: string}}
 */
test.config.index = {
    title: 'Manage Stores',
    url: test.config.url,
    __buttons: {
        addWebsite: {
            _: base._button + '[title="Create Website"]',
            __class: ['scalable', 'add', 'website'],
        },
        addStore: {
            _: base._button + '[title="Create Store"]',
            __class: ['scalable', 'add', 'store'],
        },
        addStoreView: {
            _: base._button + '[title="Create Store View"]',
            __class: ['scalable', 'add', 'storeview'],
        },
    },
}
