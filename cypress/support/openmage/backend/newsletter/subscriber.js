const defaultConfig = {
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletterSubscriber = {
    config: {
        _id: '#nav-admin-newsletter-subscriber',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Newsletter Subscribers',
            url: 'newsletter_subscriber/index',
            _grid: '#subscriberGrid_table',
        },
    },
}
