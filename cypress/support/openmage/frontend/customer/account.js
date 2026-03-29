const test = cy.openmage.test.frontend.customer.account;

/**
 * Selectors for "Account" page
 * @type {{title: string, _buttonSubmit: string, _title: string, create: {}, __fixture: string}}
 */
test.config = {
    title: 'Checks customer account create',
    _title: 'h1',
    _buttonSubmit: '#form-validate button[type="submit"]',
    __fixture: 'frontend/customer/account/create',
    create: {},
}

/**
 * Configuration for "Create an Account" page
 * @type {{title: string, url: string}}
 */
test.config.create = {
    title: 'Create an Account',
    url: '/customer/account/create',
}
