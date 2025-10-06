const defaultConfig = {
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
}

cy.testBackendUrlrewrite = {
    config: {
        _id: '#nav-admin-catalog-urlrewrite',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'URL Rewrite Management',
            url: 'urlrewrite/index',
            _grid: '#urlrewriteGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add URL Rewrite"]',
            },
        },
        edit: {
            title: 'Edit URL Rewrite',
            url: 'urlrewrite/edit',
        },
        new: {
            title: 'Add New URL Rewrite',
            url: 'urlrewrite/edit',
        },
    },
}
