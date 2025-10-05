cy.testFrontend = {
    homepage: {
        url: '/',
        newsletter: {
            _buttonSubmit: '#newsletter-validate-detail button[type="submit"]',
            _id: '#newsletter'
        }
    },
    customer: {
        account: {
            create: {
                url: '/customer/account/create',
                _buttonSubmit: '#form-validate button[type="submit"]',
                _h1: 'h1',
                h1: 'Create an Account',
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
        }
    }
}
