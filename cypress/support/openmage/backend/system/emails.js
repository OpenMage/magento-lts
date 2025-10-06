const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSystemEmailTemplate = {
    config: {
        _id: '#nav-admin-system-email_template',
        _id_parent: '#nav-admin-system',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Transactional Emails',
            url: 'system_email_template/index',
            _grid: '#systemEmailTemplateGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Template"]',
            },
        },
        edit: {
            title: 'Edit Email Template',
            url: 'system_email_template/edit',
        },
        new: {
            title: 'New Email Template',
            url: 'system_email_template/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save Template"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
                convert: defaultConfig._button + '[title="Convert to Plain Text"]',
                preview: defaultConfig._button + '[title="Preview Template"]',
            },
        },
    },
}
