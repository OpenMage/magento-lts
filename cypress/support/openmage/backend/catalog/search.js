const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendCatalogSearch = {
    config: {
        _id: '#nav-admin-catalog-search',
        _id_parent: '#nav-admin-catalog',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Search',
            url: 'catalog_search/index',
            _grid: '#catalog_search_grid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add New Search Term"]',
            },
        },
        edit: {
            title: 'Edit Search',
            url: 'catalog_search/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save Search"]',
                delete: defaultConfig._button + '[title="Delete Search"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
        new: {
            title: 'New Search',
            url: 'catalog_search/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save Search"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
