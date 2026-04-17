const test = cy.openmage.test.frontend.customer.account;

/**
 * Selectors for "Account" page
 * @type {{_buttonSubmit: string, _title: string, create: {}, edit: {}}}
 */
test.config = {
    _title: 'h1',
    _buttonSubmit: '#form-validate button[type="submit"]',
    create: {},
    edit: {},
}

/**
 * Configuration for "Create an Account" page
 * @type {{title: string, __fields: {firstname: {selector: string}, password: {selector: string}, email_address: {selector: string}, confirmation: {selector: string}, lastname: {selector: string}}, url: string}}
 */
test.config.create = {
    title: 'Create an Account',
    url: '/customer/account/create',
    __fields: {
        firstname: {
            _: '#firstname',
        },
        lastname: {
            _: '#lastname',
        },
        email_address: {
            _: '#email_address',
        },
        password: {
            _: '#password',
        },
        confirmation: {
            _: '#confirmation',
        },
    }
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
