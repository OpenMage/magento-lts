const defaultConfig = {
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
}

cy.testBackendCatalogProductsCategories = {
    config: {
        _id: '#nav-admin-catalog-categories',
        _id_parent: defaultConfig._id_parent,
        _h3: '#category-edit-container ' + defaultConfig._h3,
        index: {
            title: 'New Root Category',
            url: 'catalog_category/index',
        },
    },
}
