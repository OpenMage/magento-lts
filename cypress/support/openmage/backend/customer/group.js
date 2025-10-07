const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.group;
const tools = cy.openmage.tools;

/**
 * Buttons selectors for Edit and New page
 * @type {{save: string, back: string, reset: string}}
 * @private
 */
test.__buttons = {
    save: base._button + '[title="Save Customer Group"]',
    back: base._button + '[title="Back"]',
    reset: base._button + '[title="Reset"]',
};

/**
 * Fields selectors for Edit and New page
 * @type {{customer_group_code: {selector: string}, tax_class_id: {selector: string}}}
 * @private
 */
test.__fields = {
    customer_group_code : {
        selector: '#customer_group_code',
    },
    tax_class_id : {
        selector: '#tax_class_id',
    },
};

/**
 * Configuration for "Customer Groups" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-customer-group',
    _id_parent: '#nav-admin-customer',
    _title: base._title,
    _button: base._button,
    url: 'customer_group/index',
}

/**
 * Configuration for "Customer Groups" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.customer.group.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Customer Groups',
    url: test.config.url,
    _grid: '#customerGroupGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Customer Group"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Customer Groups button clicked');
    },
}

/**
 * Configuration for "Edit Customer Group" page
 * @type {{clickReset: cy.openmage.test.backend.customer.group.config.edit.clickReset, __buttons, clickBack: cy.openmage.test.backend.customer.group.config.edit.clickBack, clickSave: cy.openmage.test.backend.customer.group.config.edit.clickSave, title: string, __fields, url: string}}
 */
test.config.edit = {
    title: 'Edit Customer Group',
    url: 'customer_group/edit',
    __buttons: test.__buttons,
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(test.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.__buttons .reset, 'Reset button clicked');
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
        tools.click(test.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(test.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.__buttons .reset, 'Reset button clicked');
    },
}
