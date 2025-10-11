const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.online;

/**
 * Configuration for "Online Customers" menu item
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-customer-online',
    _nav: '#nav-admin-customer',
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
