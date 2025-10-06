const defaultConfig = {
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletterQueue = {
    config: {
        _id: '#nav-admin-newsletter-queue',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Newsletter Queue',
            url: 'newsletter_queue/index',
            _grid: '#queueGrid_table',
        },
    },
}
