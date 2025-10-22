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
 * @type {{
 *      title: string,
 *      url: string,
 *      _grid: string,
 *      __buttons: {add: {_: string, __class: string, click: test.config.index.__buttons.add.click}}
 * }}
 */
test.config.index = {
    title: 'Transactional Emails',
    url: test.config.url,
    _grid: '#systemEmailTemplateGrid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Template"]',
            __class: base.__buttons.add.__class,
            click: () => {
                tools.click(test.config.index.__buttons.add._, 'Add New Transactional Emails button clicked');
            },
        },
    },
}

/**
 * Configuration for "Edit Email Template" page
 */
test.config.edit = {
    title: 'Edit Email Template',
    url: 'system_email_template/edit',
    __buttons: {
        save: base.__buttons.save,
        convertToPlain: base.__buttons.convertToPlain,
        preview: base.__buttons.preview,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    __fields: test.__fields,
}

/**
 * Configuration for "New Email Template" page
 * @type {{
 *      title: string,
 *      url: string,
 *      __buttons: {
 *          preview: cy.openmage.test.backend.__base.__buttons.preview,
 *          save: cy.openmage.test.backend.__base.__buttons.save,
 *          back: cy.openmage.test.backend.__base.__buttons.back,
 *          reset: cy.openmage.test.backend.__base.__buttons.reset,
 *          convertToPlain: cy.openmage.test.backend.__base.__buttons.convertToPlain
 *      },
 *      __fields: test.config.new.__fields
 * }}
 */
test.config.new = {
    title: 'New Email Template',
    url: 'system_email_template/new',
    __buttons: {
        save: base.__buttons.save,
        convertToPlain: base.__buttons.convertToPlain,
        preview: base.__buttons.preview,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    __fields: test.__fields,
}
