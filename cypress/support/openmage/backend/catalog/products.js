const defaultConfig = {
    _button: '.content-header button'
}

cy.testBackendCatalogProducts = {
    config: {
        _id: '#nav-admin-catalog-products',
        _id_parent: '#nav-admin-catalog',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Manage Products',
            url: 'catalog_product/index',
            _grid: '#productGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add Product"]',
            },
        },
        edit: {
            title: 'Plaid Cotton',
            url: 'catalog_product/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                delete: defaultConfig._button + '[title="Delete"]',
                duplicate: defaultConfig._button + '[title="Duplicate"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
        new: {
            title: 'New Product',
            url: 'catalog_product/new',
        },
    },
}
