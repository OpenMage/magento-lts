const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.online;

/**
 * Configuration for "Online Customers" menu item
 * @type {{_title: string, _id: string, _id_parent: string, url: string, index: {}}}
 */
test.config = {
    _id: '#nav-admin-customer-online',
    _id_parent: '#nav-admin-customer',
    _title: base._title,
    url: 'customer_online/index',
    index: {},
}

/**
 * Configuration for "Online Customers" page
 * @type {{title: string, url: string}}
 */
test.config.index = {
    title: 'Online Customers',
    url: test.config.url,
}
