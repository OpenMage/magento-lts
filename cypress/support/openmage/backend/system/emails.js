const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

base.__fields = {
    template_select : {
        selector: '#template_select',
    },
    locale_select : {
        selector: '#locale_select',
    },
    template_code : {
        selector: '#template_code',
    },
    template_subject : {
        selector: '#template_subject',
    },
    template_text : {
        selector: '#template_text',
    },
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
    clickAdd: () => {
        tools.click(cy.testBackendSystemEmailTemplate.config.index.__buttons.add, 'Add New ransactional Emails button clicked');
    },
}

cy.testBackendSystemEmailTemplate.config.edit = {
    title: 'Edit Email Template',
    url: 'system_email_template/edit',
    __buttons: {
        save: base._button + '[title="Save Template"]',
        convert: base._button + '[title="Convert to Plain Text"]',
        preview: base._button + '[title="Preview Template"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
}

cy.testBackendSystemEmailTemplate.config.new = {
    title: 'New Email Template',
    url: 'system_email_template/new',
    __buttons: {
        save: base._button + '[title="Save Template"]',
        convert: base._button + '[title="Convert to Plain Text"]',
        preview: base._button + '[title="Preview Template"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendSystemEmailTemplate.config.edit.__buttons.save, 'Save button clicked');
    },
    clickCovert: () => {
        tools.click(cy.testBackendSystemEmailTemplate.config.edit.__buttons.convert, 'Covert button clicked');
    },
    clickPreview: () => {
        tools.click(cy.testBackendSystemEmailTemplate.config.edit.__buttons.preview, 'Prieview button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendSystemEmailTemplate.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendSystemEmailTemplate.config.edit.__buttons.reset, 'Reset button clicked');
    },
}
