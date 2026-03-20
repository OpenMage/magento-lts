const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.customer;
const tools = cy.openmage.tools;

/**
 * Configuration for "Manage Customers" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-customer-manage',
    _nav: '#nav-admin-customer',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/customer/customer',
    url: 'admin/customer',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Manage Customers" page
 * @type {{title: string, url: string, grid: {}, __buttons: {}, clickGridRow: (function(*=, *=): void)}}
 */
test.config.index = {
    title: 'Manage Customers',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'entity_id', dir: 'desc' } }},
    __buttons: {},
    clickGridRow: (content = '', _ = 'td') => {
        tools.grid.clickContains(test.config.index, content, _);
    },
}

/**
 * Configuration for buttons on "Manage Customers" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.customer.customer.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Customer"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Customers button clicked');
        },
    },
}

/**
 * Configuration for "Edit Customer" page
 * @type {{__buttons: {createOrder: string, save: string, back: string, reset: string, saveAndContinue: string, delete: string}, title: string, url: string}}
 */
test.config.edit = {
    // comes from sample data
    // TODO: make dynamic, update template
    title: 'John Doe',
    url: 'customer/edit',
    __buttons: {
        save: base.__buttons.save,
        saveAndContinue: base.__buttons.saveAndContinue,
        delete: base.__buttons.delete,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
        createOrder: {
            _: base._button + '[title="Create Order"]',
            __class: ['scalable', 'add', 'create-order'],
        },
    },
}

/**
 * Configuration for "New Customer" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new}}
 */
test.config.new = {
    title: 'New Customer',
    url: 'customer/new',
    __buttons: base.__buttonsSets.new,
}
