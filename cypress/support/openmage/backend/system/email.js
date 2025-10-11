const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.email;
const tools = cy.openmage.tools;

/**
 * Common fields for "Edit Email Template" and "New Email Template" pages
 * @type {{template_subject: {_: string}, locale_select: {_: string}, template_text: {_: string}, template_select: {_: string}, template_code: {_: string}}}
 * @private
 */
test.__fields = {
    template_select : {
        _: '#template_select',
    },
    locale_select : {
        _: '#locale_select',
    },
    template_code : {
        _: '#template_code',
    },
    template_subject : {
        _: '#template_subject',
    },
    template_text : {
        _: '#template_text',
    },
}

/**
 * Configuration for "Transactional Emails" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-system-email_template',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_email_template/index',
    index: {},
    edit: {},
    new: {},
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
        add: {
            _: base._button + '[title="Add New Template"]',
            __class: base.__buttons.add.__class,
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Transactional Emails button clicked');
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
        save: base.__buttons.save,
        convert: {
            _: base._button + '[title="Convert to Plain Text"]',
            __class: ['scalable', 'save', 'convert'],
        },
        preview: {
            _: base._button + '[title="Preview Template"]',
            __class: ['scalable', 'task', 'preview'],
        },
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    __fields: test.__fields,
}

/**
 * Configuration for "New Email Template" page
 * @type {{clickPreview: cy.openmage.test.backend.system.email.config.new.clickPreview, __buttons: {preview: string, save: string, back: string, reset: string, convertToPlain: string}, clickCovert: cy.openmage.test.backend.system.email.config.new.clickCovert, title: string, __fields: *, url: string}}
 */
test.config.new = {
    title: 'New Email Template',
    url: 'system_email_template/new',
    __buttons: {
        save: base.__buttons.save,
        convertToPlain: {
            _: base._button + '[title="Convert to Plain Text"]',
            __class: ['scalable', 'task', 'to-plain'],
        },
        preview: {
            _: base._button + '[title="Preview Template"]',
            __class: ['scalable', 'task', 'preview'],
        },
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    __fields: test.__fields,
    clickCovert: () => {
        tools.click(test.config.edit.__buttons.convert._, 'Convert button clicked');
    },
    clickPreview: () => {
        tools.click(test.config.edit.__buttons.preview._, 'Preview button clicked');
    },
}
