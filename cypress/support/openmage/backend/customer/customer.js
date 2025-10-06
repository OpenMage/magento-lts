const defaultConfig = {
    _button: '.form-buttons button'
}

cy.testBackendCustomerCustomer = {
    config: {
        _id: '#nav-admin-customer-manage',
        _id_parent: '#nav-admin-customer',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Manage Customers',
            url: 'customer/index',
            _grid: '#customerGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add New Customer"]',
            },
        },
        edit: {
            title: 'John Smith',
            url: 'customer/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save Customer"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                delete: defaultConfig._button + '[title="Delete Customer"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
                createOrder: defaultConfig._button + '[title="Create Order"]',
            },
        },
        new: {
            title: 'New Customer',
            url: 'customer/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save Customer"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
