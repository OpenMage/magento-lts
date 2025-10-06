const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemEmailTemplate = {
    config: {
        _id: '#nav-admin-system-email_template',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
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
        },
    },
}
