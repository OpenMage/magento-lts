const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

cy.testBackendNewsletterTemplates = {};

cy.testBackendNewsletterTemplates.config = {
    _id: '#nav-admin-newsletter-template',
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
    _button: base._button,
};

cy.testBackendNewsletterTemplates.config.index = {
    title: 'Newsletter Templates',
    url: 'newsletter_template/index',
    _grid: '#newsletterTemplateGrid_table',
    __buttons: {
        add: '.form-buttons button[title="Add New Template"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendNewsletterTemplates.config.index.__buttons.add, 'Add New Newsletter Templates button clicked');
    },
}

cy.testBackendNewsletterTemplates.config.edit = {
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

cy.testBackendNewsletterTemplates.config.new = {
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
