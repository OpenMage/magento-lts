const defaultConfig = {
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletter = {
    templates: {
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
    queue: {
        _id: '#nav-admin-newsletter-queue',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Newsletter Queue',
            url: 'newsletter_queue/index',
            _grid: '#queueGrid_table',
        },
    },
    subscriber: {
        _id: '#nav-admin-newsletter-subscriber',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Newsletter Subscribers',
            url: 'newsletter_subscriber/index',
            _grid: '#subscriberGrid_table',
        },
    },
    report: {
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
