const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.product;
const tools = cy.openmage.tools;

/**
 * Configuration for "Products" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-catalog-products',
    _nav: '#nav-admin-catalog',
    _title: base._title,
    _button: '.content-header button', // Custom base button selector
    url: 'catalog_product/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Manage Products" page
 * @type {{__buttons: {add: {_: string}}, title: string, url: string, _grid: string, clickAdd: (function(): void)}}
 */
test.config.index = {
    title: 'Manage Products',
    url: test.config.url,
    _grid: '#productGrid_table',
    __buttons: {
        add: {
            _: test.config._button + '[title="Add Product"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Products button clicked');
    },
}

/**
 * Configuration for "Edit Product" page
 * @type {{title: string, url: string, __buttons: {save: {_: string}, saveAndContinue: {_: string}, delete: {_: string}, back: {_: string}, reset: {_: string}, duplicate: {_: string}}}}
 * TODO: rmove dupluctate require-entry class from fields
 */
test.config.edit = {
    title: 'Plaid Cotton',
    url: 'catalog_product/edit',
    __buttons: {
        save: {
            _: test.config._button + '[title="Save"]',
        },
        saveAndContinue: {
            _: test.config._button + '[title="Save and Continue Edit"]',
        },
        delete: {
            _: test.config._button + '[title="Delete"]',
        },
        back: {
            _: test.config._button + '[title="Back"]',
        },
        reset: {
            _: test.config._button + '[title="Reset"]',
        },
        duplicate: {
            _: test.config._button + '[title="Duplicate"]',
        }
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
