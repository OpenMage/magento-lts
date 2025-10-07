const base = {
    _button: '.form-buttons button'
}

cy.testBackendCustomerCustomer = {};

cy.testBackendCustomerCustomer.config = {
    _id: '#nav-admin-customer-manage',
    _id_parent: '#nav-admin-customer',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCustomerCustomer.config.index = {
    title: 'Manage Customers',
    url: 'customer/index',
    _grid: '#customerGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Customer"]',
    },
}

cy.testBackendCustomerCustomer.config.edit = {
    title: 'John Smith', // comes from sample data
    url: 'customer/edit',
    __buttons: {
        save: base._button + '[title="Save Customer"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete Customer"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
        createOrder: base._button + '[title="Create Order"]',
    },
}

cy.testBackendCustomerCustomer.config.new = {
    title: 'New Customer',
    url: 'customer/new',
    __buttons: {
        save: base._button + '[title="Save Customer"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}
