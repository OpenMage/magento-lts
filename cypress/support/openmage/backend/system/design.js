const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSystemDesign = {
    config: {
        _id: '#nav-admin-system-design',
        _id_parent: '#nav-admin-system',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Design',
            url: 'system_design/index',
            _grid: '#designGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add Design Change"]',
            },
        },
        edit: {
            title: 'Edit Design Change',
            url: 'system_design/edit',
        },
        new: {
            title: 'New Design Change',
            url: 'system_design/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                back: defaultConfig._button + '[title="Back"]',
            },
        },
    },
}
