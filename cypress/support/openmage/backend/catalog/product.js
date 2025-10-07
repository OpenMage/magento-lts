const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.product;
const tools = cy.openmage.tools;

/**
 * Configuration for "Products" menu item
 * @type {{_button: string, _title: string, _id_parent: string, _id: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-catalog-products',
    _id_parent: '#nav-admin-catalog',
    _title: base._title,
    _button: '.content-header button', // Custom base button selector
    url: 'catalog_product/index',
}

/**
 * Configuration for "Manage Products" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: test.products.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Manage Products',
    url: test.config.url,
    _grid: '#productGrid_table',
    __buttons: {
        add: test.config._button + '[title="Add Product"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Products button clicked');
    },
}

/**
 * Configuration for "Edit Product" page
 * @type {{title: string, url: string, __buttons: {save: string, reset: string, delete: string, back: string, duplicate: string}}}
 * TODO: rmove dupluctate require-entry class from fields
 */
test.config.edit = {
    title: 'Plaid Cotton',
    url: 'catalog_product/edit',
    __buttons: {
        save: test.config._button + '[title="Save"]',
        saveAndContinue: test.config._button + '[title="Save and Continue Edit"]',
        delete: test.config._button + '[title="Delete"]',
        back: test.config._button + '[title="Back"]',
        reset: test.config._button + '[title="Reset"]',
        duplicate: test.config._button + '[title="Duplicate"]'
    },
}

/**
 * Configuration for "New Product" page
 * @type {{title: string, url: string}}
 */
test.config.new = {
    title: 'New Product',
    url: 'catalog_product/new',
}
