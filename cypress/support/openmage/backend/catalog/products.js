const tools = cy.openmage.tools;

const base = {
    _button: '.content-header button'
}

cy.testBackendCatalogProducts = {};

cy.testBackendCatalogProducts.config = {
    _id: '#nav-admin-catalog-products',
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCatalogProducts.config.index = {
    title: 'Manage Products',
    url: 'catalog_product/index',
    _grid: '#productGrid_table',
    __buttons: {
        add: base._button + '[title="Add Product"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendCatalogProducts.config.index.__buttons.add, 'Add New Products button clicked');
    },
}

cy.testBackendCatalogProducts.config.edit = {
    title: 'Plaid Cotton',
    url: 'catalog_product/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete"]',
        duplicate: base._button + '[title="Duplicate"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}

cy.testBackendCatalogProducts.config.new = {
    title: 'New Product',
    url: 'catalog_product/new',
}
