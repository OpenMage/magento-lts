const defaultConfig = {
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletterReport = {
    config: {
        _id: '#nav-admin-newsletter-problem',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Newsletter Problem Reports',
            url: 'newsletter_problem/index',
            _grid: '#problemGrid_table',
        },
    },
}
