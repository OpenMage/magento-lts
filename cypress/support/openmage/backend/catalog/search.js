const base = {
    _button: '.form-buttons button',
}

cy.testBackendCatalogSearch = {};

cy.testBackendCatalogSearch.config = {
    _id: '#nav-admin-catalog-search',
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCatalogSearch.config.index = {
    title: 'Search',
    url: 'catalog_search/index',
    _grid: '#catalog_search_grid_table',
    __buttons: {
        add: base._button + '[title="Add New Search Term"]',
    },
}

cy.testBackendCatalogSearch.config.edit = {
    title: 'Edit Search',
    url: 'catalog_search/edit',
    __buttons: {
        save: base._button + '[title="Save Search"]',
        delete: base._button + '[title="Delete Search"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}

cy.testBackendCatalogSearch.config.new = {
    title: 'New Search',
    url: 'catalog_search/new',
    __buttons: {
        save: base._button + '[title="Save Search"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}
