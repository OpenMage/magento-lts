const test = cy.openmage.test.frontend.customer.account;

/**
 * Selectors for "Account" page
 * @type {{_buttonSubmit: string, _title: string}}
 */
test.config = {
    _title: 'h1',
    _buttonSubmit: '#form-validate button[type="submit"]',
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
            selector: '#firstname',
        },
        lastname: {
            selector: '#lastname',
        },
        email_address: {
            selector: '#email_address',
        },
        password: {
            selector: '#password',
        },
        confirmation: {
            selector: '#confirmation',
        },
    }
}
