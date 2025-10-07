const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.dashbord;

/**
 * Configuration for "Dashboard" menu item
 * @type {{_title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-dashboard',
    _id_parent: '#nav-admin-dashboard',
    _title: 'h3.head-dashboard',
    url: 'dashboard/index',
}

/**
 * Configuration for "Dashboard" page
 * @type {{title: string, url: string}}
 */
test.config.index = {
    title: 'Dashboard',
    url: test.config.url,
}
