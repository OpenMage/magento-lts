const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemMyAccount = {
    config: {
        _id: '#nav-admin-system-myaccount',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'My Account',
            url: 'system_account/index',
            __buttons: {
                save: '.form-buttons button[title="Save Account"]',
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
