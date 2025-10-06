const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSystemMyAccount = {
    config: {
        _id: '#nav-admin-system-myaccount',
        _id_parent: '#nav-admin-system',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'My Account',
            url: 'system_account/index',
            __buttons: {
                save: defaultConfig._button + '[title="Save Account"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
            __validation: {
                _input: {
                    username: '#username',
                    firstname: '#firstname',
                    lastname: '#lastname',
                    email: '#email',
                    current_password: '#current_password',
                }
            }
        },
    },
}
