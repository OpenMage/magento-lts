const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemDesign = {
    config: {
        _id: '#nav-admin-system-design',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Design',
            url: 'system_design/index',
            _grid: '#designGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add Design Change"]',
            },
        },
        edit: {
            title: 'Edit Design Change',
            url: 'system_design/edit',
        },
        new: {
            title: 'New Design Change',
            url: 'system_design/new',
        },
    },
}
