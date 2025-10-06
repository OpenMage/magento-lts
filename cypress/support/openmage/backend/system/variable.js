const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemVariable = {
    config: {
        _id: '#nav-admin-system-variable',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
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
        },
    },
}
