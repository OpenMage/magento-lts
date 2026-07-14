const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.email;
const tools = cy.openmage.tools;

/**
 * Configuration for "Transactional Emails" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-system-email_template',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/system/email',
    url: 'admin/system_email_template',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Transactional Emails" page
 * @type {{
 *      title: string,
 *      url: string,
 *      grid: {},
 * }}
 */
test.config.index = {
    title: 'Transactional Emails',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'template_id', dir: 'asc' } }},
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
    }
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
 *      }
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
    }
}
