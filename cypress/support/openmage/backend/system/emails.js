const base = {
    _button: '.form-buttons button',
}

cy.testBackendSystemEmailTemplate = {};

cy.testBackendSystemEmailTemplate.config = {
    _id: '#nav-admin-system-email_template',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendSystemEmailTemplate.config.index = {
    title: 'Transactional Emails',
    url: 'system_email_template/index',
    _grid: '#systemEmailTemplateGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Template"]',
    },
}

cy.testBackendSystemEmailTemplate.config.edit = {
    title: 'Edit Email Template',
    url: 'system_email_template/edit',
}

cy.testBackendSystemEmailTemplate.config.new = {
    title: 'New Email Template',
    url: 'system_email_template/new',
    __buttons: {
        save: base._button + '[title="Save Template"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
        convert: base._button + '[title="Convert to Plain Text"]',
        preview: base._button + '[title="Preview Template"]',
    },
}
