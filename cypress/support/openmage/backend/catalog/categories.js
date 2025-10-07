const base = {
    _button: '.form-buttons button'
}

cy.testBackendCatalogProductsCategories = {};

cy.testBackendCatalogProductsCategories.config = {
    _id: '#nav-admin-catalog-categories',
    _id_parent: '#nav-admin-catalog',
    _h3: '#category-edit-container h3.icon-head',
    _button: base._button,
}

cy.testBackendCatalogProductsCategories.config.index = {
    title: 'New Root Category',
    url: 'catalog_category/index',
    __buttons: {
        save: base._button + '[title="Save Category"]',
        reset: base._button + '[title="Reset"]',
    },
}
