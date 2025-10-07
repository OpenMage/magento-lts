const base = {
    _button: '.form-buttons button',
}

cy.testBackendSystemMyAccount = {};

cy.testBackendSystemMyAccount.config = {
    _id: '#nav-admin-system-myaccount',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendSystemMyAccount.config.index = {
    title: 'My Account',
    url: 'system_account/index',
    __buttons: {
        save: base._button + '[title="Save Account"]',
        reset: base._button + '[title="Reset"]',
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
}
