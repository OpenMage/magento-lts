const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.template;
const tools = cy.openmage.tools;

/**
 * Configuration for "Newsletter Templates" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-newsletter-template',
    _id_parent: '#nav-admin-newsletter',
    _title: base._title,
    _button: base._button,
    url: 'newsletter_template/index',
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
        add: '.form-buttons button[title="Add New Template"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Newsletter Templates button clicked');
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
        save: base._button + '[title="Save Template"]',
        saveAs: base._button + '[title="Save As"]',
        delete: base._button + '[title="Delete Template"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
        convert: base._button + '[title="Convert to Plain Text"]',
        preview: base._button + '[title="Preview Template"]',
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
        save: base._button + '[title="Save Template"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
        convert: base._button + '[title="Convert to Plain Text"]',
        preview: base._button + '[title="Preview Template"]',
    },
}
