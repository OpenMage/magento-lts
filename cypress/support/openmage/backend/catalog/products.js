const defaultConfig = {
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
}

cy.testBackendCatalogProducts = {
    config: {
        _id: '#nav-admin-catalog-products',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Products',
            url: 'catalog_product/index',
            _grid: '#productGrid_table',
            __buttons: {
                add: 'button[title="Add Product"]',
            },
        },
        edit: {
            title: 'Plaid Cotton',
            url: 'catalog_product/edit',
        },
        new: {
            title: 'New Product',
            url: 'catalog_product/new',
        },
    },
}
