const defaultConfig = {
    _button: '.form-buttons button'
}

cy.testBackendCustomerGroups = {
    config: {
        _id: '#nav-admin-customer-group',
        _id_parent: '#nav-admin-customer',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Customer Groups',
            url: 'customer_group/index',
            _grid: '#customerGroupGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add New Customer Group"]',
            },
        },
        edit: {
            title: 'Edit Customer Group',
            url: 'customer_group/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save Customer Group"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
        new: {
            title: 'New Customer Group',
            url: 'customer_group/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save Customer Group"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
