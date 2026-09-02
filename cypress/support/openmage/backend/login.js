const test = cy.openmage.test.backend.login;

/**
 * Configuration for "Dashboard" menu item
 * @type {{url: string, __fixture: string}}
 */
test.config = {
    url: '/admin',
    __fixture: 'backend/login',
}
