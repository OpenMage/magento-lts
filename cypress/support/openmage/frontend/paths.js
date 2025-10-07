cy.testFrontend = {};

cy.testFrontend.homepage = {
    url: '/',
};

cy.testFrontend.homepage.newsletter = {
    _buttonSubmit: '#newsletter-validate-detail button[type="submit"]',
    _id: '#newsletter'
}

cy.testFrontend.customer = {}
cy.testFrontend.customer.account = {};

cy.testFrontend.customer.account.create = {
    h1: 'Create an Account',
    url: '/customer/account/create',
    _h1: 'h1',
    _buttonSubmit: '#form-validate button[type="submit"]',
    __validation: {
        _input: {
            firstname: '#firstname',
            lastname: '#lastname',
            email_address: '#email_address',
            password: '#password',
            confirmation: '#confirmation',
        }
    }
}
