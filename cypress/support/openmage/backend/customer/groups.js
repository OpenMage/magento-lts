const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button'
}

base.__buttons = {
    save: base._button + '[title="Save Customer Group"]',
    back: base._button + '[title="Back"]',
    reset: base._button + '[title="Reset"]',
};

base.__fields = {
    customer_group_code : {
        selector: '#customer_group_code',
    },
    tax_class_id : {
        selector: '#tax_class_id',
    },
};

cy.testBackendCustomerGroups = {};

cy.testBackendCustomerGroups.config = {
    _id: '#nav-admin-customer-group',
    _id_parent: '#nav-admin-customer',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCustomerGroups.config.index = {
    title: 'Customer Groups',
    url: 'customer_group/index',
    _grid: '#customerGroupGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Customer Group"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendCustomerGroups.config.index.__buttons.add, 'Add New Customer Groups button clicked');
    },
}

cy.testBackendCustomerGroups.config.edit = {
    title: 'Edit Customer Group',
    url: 'customer_group/edit',
    __buttons: base.__buttons,
    __fields: base.__fields,
    clickSave: () => {
        tools.click(base.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(base.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(base.__buttons .reset, 'Reset button clicked');
    },
}

cy.testBackendCustomerGroups.config.new = {
    title: 'New Customer Group',
    url: 'customer_group/new',
    __buttons: base.__buttons,
    __fields: base.__fields,
    clickSave: () => {
        tools.click(base.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(base.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(base.__buttons .reset, 'Reset button clicked');
    },
}
