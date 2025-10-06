const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendNewsletterTemplates = {
    config: {
        _id: '#nav-admin-newsletter-template',
        _id_parent: '#nav-admin-newsletter',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Newsletter Templates',
            url: 'newsletter_template/index',
            _grid: '#newsletterTemplateGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Template"]',
            },
        },
        edit: {
            title: 'Edit Newsletter Template',
            url: 'newsletter_template/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save Template"]',
                saveAs: defaultConfig._button + '[title="Save As"]',
                delete: defaultConfig._button + '[title="Delete Template"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
                convert: defaultConfig._button + '[title="Convert to Plain Text"]',
                preview: defaultConfig._button + '[title="Preview Template"]',
            },
        },
        new: {
            title: 'New Newsletter Template',
            url: 'newsletter_template/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save Template"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
                convert: defaultConfig._button + '[title="Convert to Plain Text"]',
                preview: defaultConfig._button + '[title="Preview Template"]',
            },
        },
    },
}
