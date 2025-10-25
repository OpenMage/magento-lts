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
    _button: base._button,
    url: 'catalog_product/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Manage Products" page
 * @type {{title: string, url: string, _grid: string, __buttons: {})}}
 */
test.config.index = {
    title: 'Manage Products',
    url: test.config.url,
    _grid: '#productGrid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Manage Products" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.catalog.product.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: test.config._button + '[title="Add Product"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Products button clicked');
        },
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
        save: base.__buttons.save,
        saveAndContinue: base.__buttons.saveAndContinue,
        delete: base.__buttons.delete,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
        duplicate: {
            _: test.config._button + '[title="Duplicate"]',
            __class: ['scalable', 'add', 'duplicate'],
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
    __buttons: {
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
}
