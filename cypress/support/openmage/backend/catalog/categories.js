const defaultConfig = {
    _button: '.form-buttons button'
}

cy.testBackendCatalogProductsCategories = {
    config: {
        _id: '#nav-admin-catalog-categories',
        _id_parent: '#nav-admin-catalog',
        _h3: '#category-edit-container h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'New Root Category',
            url: 'catalog_category/index',
            __buttons: {
                save: defaultConfig._button + '[title="Save Category"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
