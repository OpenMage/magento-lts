const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.template;
const tools = cy.openmage.tools;

/**
 * Configuration for "Newsletter Templates" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-newsletter-template',
    _nav: '#nav-admin-newsletter',
    _title: base._title,
    _button: base._button,
    url: 'newsletter_template/index',
    index: {},
    edit: {},
    new: {},
};

/**
 * Configuration for "Newsletter Templates" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Newsletter Templates',
    url: test.config.url,
    _grid: '#newsletterTemplateGrid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Newsletter Templates" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.newsletter.template.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Template"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Newsletter Templates button clicked');
        },
    },
}

/**
 * Configuration for "Edit Newsletter Template" page
 * @type {{__buttons: {preview: string, saveAs: string, save: string, back: string, reset: string, convert: string, delete: string}, title: string, url: string}}
 */
test.config.edit = {
    title: 'Edit Newsletter Template',
    url: 'newsletter_template/edit',
    __buttons: {
        save: base.__buttons.save,
        saveAs: {
            _: base._button + '[title="Save As"]',
            __class: ['scalable', 'save', 'save-as'],
            click: () => {
                cy.openmage.tools.click(test.config.edit.__buttons.saveAs._, 'Save as button clicked');
            },
        },
        delete: base.__buttons.delete,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
        convertToPlain: base.__buttons.convertToPlain,
        preview: base.__buttons.preview,
    },
}

/**
 * Configuration for "New Newsletter Template" page
 * @type {{__buttons: {preview: string, save: string, back: string, reset: string, convert: string}, title: string, url: string}}
 */
test.config.new = {
    title: 'New Newsletter Template',
    url: 'newsletter_template/new',
    __buttons: {
        save: base.__buttons.save,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
        convertToPlain: base.__buttons.convertToPlain,
        preview: base.__buttons.preview,
    },
}
