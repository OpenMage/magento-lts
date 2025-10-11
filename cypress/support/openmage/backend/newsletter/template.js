const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.template;
const tools = cy.openmage.tools;

/**
 * Configuration for "Newsletter Templates" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _id: '#nav-admin-newsletter-template',
    _id_parent: '#nav-admin-newsletter',
    _title: base._title,
    _button: base._button,
    url: 'newsletter_template/index',
    index: {},
    edit: {},
    new: {},
};

/**
 * Configuration for "Newsletter Templates" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.newsletter.template.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Newsletter Templates',
    url: test.config.url,
    _grid: '#newsletterTemplateGrid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Template"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Newsletter Templates button clicked');
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
        save: {
            _: base._button + '[title="Save Template"]',
        },
        saveAs: {
            _: base._button + '[title="Save As"]',
        },
        delete: {
            _: base._button + '[title="Delete Template"]',
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
        convert: {
            _: base._button + '[title="Convert to Plain Text"]',
        },
        preview: {
            _: base._button + '[title="Preview Template"]',
        },
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
        save: {
            _: base._button + '[title="Save Template"]',
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
        convert: {
            _: base._button + '[title="Convert to Plain Text"]',
        },
        preview: {
            _: base._button + '[title="Preview Template"]',
        },
    },
    clickSave: () => {
        tools.click(test.config.new.__buttons.save._, 'Save button clicked');
    },
}
