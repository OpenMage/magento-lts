const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendUrlrewrite = {
    config: {
        _id: '#nav-admin-catalog-urlrewrite',
        _id_parent: '#nav-admin-catalog',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'URL Rewrite Management',
            url: 'urlrewrite/index',
            _grid: '#urlrewriteGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add URL Rewrite"]',
            },
        },
        edit: {
            title: 'Edit URL Rewrite',
            url: 'urlrewrite/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                delete: defaultConfig._button + '[title="Delete"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
        new: {
            title: 'Add New URL Rewrite',
            url: 'urlrewrite/edit',
            __buttons: {
                back: defaultConfig._button + '[title="Back"]',
            },
        },
    },
}
