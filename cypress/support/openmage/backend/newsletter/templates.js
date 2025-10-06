const defaultConfig = {
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletterTemplates = {
    config: {
        _id: '#nav-admin-newsletter-template',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
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
        },
        new: {
            title: 'New Newsletter Template',
            url: 'newsletter_template/new',
        },
    },
}
