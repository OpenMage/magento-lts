const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.group;
const tools = cy.openmage.tools;

/**
 * Fields selectors for Edit and New page
 * @type {{customer_group_code: {_: string}, tax_class_id: {_: string}}}
 * @private
 */
test.__fields = {
    customer_group_code : {
        _: '#customer_group_code',
    },
    tax_class_id : {
        _: '#tax_class_id',
    },
};

/**
 * Configuration for "Customer Groups" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-customer-group',
    _nav: '#nav-admin-customer',
    _title: base._title,
    _button: base._button,
    url: 'customer_group/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Customer Groups" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}, clickGridRow: (function(*=, *=): void)}}
 */
test.config.index = {
    title: 'Customer Groups',
    url: test.config.url,
    _grid: '#customerGroupGrid_table',
    __buttons: {},
    clickGridRow: (content = '', selector = 'td') => {
        tools.grid.clickContains(test.config.index, content, selector);
    },
}

/**
 * Configuration for buttons on "Customer Groups" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.customer.group.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Customer Group"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Customer Groups button clicked');
        },
    },
}

/**
 * Configuration for "Edit Customer Group" page
  * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.editNoContinue, __fields: test.config.edit.__fields}}
 * TODO: there can be 3 buttons, when group is deletable
 */
test.config.edit = {
    title: 'Edit Customer Group',
    url: 'customer_group/edit',
    __buttons: base.__buttonsSets.editNoContinue,
    __fields: test.__fields,
}

/**
 * Configuration for "New Customer Group" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.newNoContinue, __fields: test.config.new.__fields}}
 */
test.config.new = {
    title: 'New Customer Group',
    url: 'customer_group/new',
    __buttons: base.__buttonsSets.newNoContinue,
    __fields: test.__fields,
}
