const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSystemVariable = {
    config: {
        _id: '#nav-admin-system-variable',
        _id_parent: '#nav-admin-system',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Custom Variables',
            url: 'system_variable/index',
            _grid: '#customVariablesGrid',
            __buttons: {
                add: '.form-buttons button[title="Add New Variable"]',
            },
        },
        edit: {
            title: 'Custom Variable',
            url: 'system_variable/edit',
        },
        new: {
            title: 'New Custom Variable',
            url: 'system_variable/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
