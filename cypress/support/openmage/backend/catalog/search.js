const defaultConfig = {
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
}

cy.testBackendCatalogSearch = {
    config: {
        _id: '#nav-admin-catalog-search',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Search',
            url: 'catalog_search/index',
            _grid: '#catalog_search_grid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Search Term"]',
            },
        },
        edit: {
            title: 'Edit Search',
            url: 'catalog_search/edit',
        },
        new: {
            title: 'New Search',
            url: 'catalog_search/new',
        },
    },
}
