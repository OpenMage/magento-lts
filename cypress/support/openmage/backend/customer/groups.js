const base = {
    _button: '.form-buttons button'
}

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
}

cy.testBackendCustomerGroups.config.edit = {
    title: 'Edit Customer Group',
    url: 'customer_group/edit',
    __buttons: {
        save: base._button + '[title="Save Customer Group"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}

cy.testBackendCustomerGroups.config.new = {
    title: 'New Customer Group',
    url: 'customer_group/new',
    __buttons: {
        save: base._button + '[title="Save Customer Group"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}
