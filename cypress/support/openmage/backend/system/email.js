const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.email;
const tools = cy.openmage.tools;

/**
 * Common fields for "Edit Email Template" and "New Email Template" pages
 * @type {{template_subject: {selector: string}, locale_select: {selector: string}, template_text: {selector: string}, template_select: {selector: string}, template_code: {selector: string}}}
 * @private
 */
test.__fields = {
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

/**
 * Configuration for "Transactional Emails" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-email_template',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_email_template/index',
}

/**
 * Configuration for "Transactional Emails" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.system.email.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Transactional Emails',
    url: test.config.url,
    _grid: '#systemEmailTemplateGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Template"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New ransactional Emails button clicked');
    },
}

/**
 * Configuration for "Edit Email Template" page
 * @type {{__buttons: {preview: string, save: string, back: string, reset: string, convert: string}, title: string, __fields: *, url: string}}
 */
test.config.edit = {
    title: 'Edit Email Template',
    url: 'system_email_template/edit',
    __buttons: {
        save: base._button + '[title="Save Template"]',
        convert: base._button + '[title="Convert to Plain Text"]',
        preview: base._button + '[title="Preview Template"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
}

/**
 * Configuration for "New Email Template" page
 * @type {{clickCovert: cy.openmage.test.backend.system.email.config.new.clickCovert, clickPreview: cy.openmage.test.backend.system.email.config.new.clickPreview, clickReset: cy.openmage.test.backend.system.email.config.new.clickReset, __buttons: {preview: string, save: string, back: string, reset: string, convert: string}, clickBack: cy.openmage.test.backend.system.email.config.new.clickBack, clickSave: cy.openmage.test.backend.system.email.config.new.clickSave, title: string, __fields: *, url: string}}
 */
test.config.new = {
    title: 'New Email Template',
    url: 'system_email_template/new',
    __buttons: {
        save: base._button + '[title="Save Template"]',
        convert: base._button + '[title="Convert to Plain Text"]',
        preview: base._button + '[title="Preview Template"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save, 'Save button clicked');
    },
    clickCovert: () => {
        tools.click(test.config.edit.__buttons.convert, 'Covert button clicked');
    },
    clickPreview: () => {
        tools.click(test.config.edit.__buttons.preview, 'Prieview button clicked');
    },
    clickBack: () => {
        tools.click(test.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.edit.__buttons.reset, 'Reset button clicked');
    },
}
