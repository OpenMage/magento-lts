const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.dashbord;

/**
 * Configuration for "Dashboard" menu item
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-dashboard',
    _nav: '#nav-admin-dashboard',
    _title: 'h3.head-dashboard',
    url: 'dashboard/index',
    index: {},
}

/**
 * Configuration for "Dashboard" page
 * @type {{title: string, url: string}}
 */
test.config.index = {
    title: 'Dashboard',
    url: test.config.url,
}
