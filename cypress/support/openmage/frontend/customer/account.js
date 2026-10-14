const test = cy.openmage.test.frontend.customer.account;

/**
 * Selectors for "Account" page
 * @type {{title: string, _buttonSubmit: string, _title: string, create: {}, edit: {}, __fixture: string}}
 */
test.config = {
    title: 'Checks customer account create',
    _title: 'h1',
    _buttonSubmit: '#form-validate button[type="submit"]',
    __fixture: 'frontend/customer/account/create',
    create: {},
    edit: {},
}

/**
 * Configuration for "Create an Account" page
 * @type {{title: string, url: string}}
 */
test.config.create = {
    title: 'Create an Account',
    url: '/customer/account/create',
}

/**
 * Configuration for "Edit an Account" page
 * @type {{title: string, __fields: {current_password: {selector: string}}, url: string}}
 */
test.config.edit = {
    title: 'Edit an Account',
    url: '/customer/account/edit',
    __fields: {
        current_password: {
            _: '#current_password',
        },
        password: {
            _: '#password',
        },
        confirmation: {
            _: '#confirmation',
        },
    }
}
