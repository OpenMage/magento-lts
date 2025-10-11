const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.group;
const tools = cy.openmage.tools;

/**
 * Buttons selectors for Edit and New page
 * @type {{save: string, back: string, reset: string}}
 * @private
 */
test.__buttons = {
    save: {
        _: base._button + '[title="Save Customer Group"]',
        __class: base.__buttons.save.__class,
    },
    back: {
        _: base.__buttons.back._,
        __class: base.__buttons.back.__class,
    },
    reset: {
        _: base.__buttons.reset._,
        __class: base.__buttons.reset.__class,
    },
};

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
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.customer.group.config.index.clickAdd, clickGridRow: cy.openmage.test.backend.customer.group.config.index.clickGridRow}}
 */
test.config.index = {
    title: 'Customer Groups',
    url: test.config.url,
    _grid: '#customerGroupGrid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Customer Group"]',
            __class: base.__buttons.add.__class,
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Customer Groups button clicked');
    },
    clickGridRow: (content = '', selector = 'td') => {
        tools.grid.clickContains(test.config.index, content, selector);
    },
}

/**
 * Configuration for "Edit Customer Group" page
 * @type {{clickReset: cy.openmage.test.backend.customer.group.config.edit.clickReset, __buttons, clickBack: cy.openmage.test.backend.customer.group.config.edit.clickBack, clickSave: cy.openmage.test.backend.customer.group.config.edit.clickSave, title: string, __fields, url: string}}
 * TODO: there can be 4 buttons, when group is deletable
 */
test.config.edit = {
    title: 'Edit Customer Group',
    url: 'customer_group/edit',
    __buttons: test.__buttons,
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save._, 'Save button clicked');
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
    },
}

/**
 * Configuration for "New Customer Group" page
 * @type {{clickReset: cy.openmage.test.backend.customer.group.config.new.clickReset, __buttons, clickBack: cy.openmage.test.backend.customer.group.config.new.clickBack, clickSave: cy.openmage.test.backend.customer.group.config.new.clickSave, title: string, __fields, url: string}}
 */
test.config.new = {
    title: 'New Customer Group',
    url: 'customer_group/new',
    __buttons: test.__buttons,
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save._, 'Save button clicked');
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
    },
}
