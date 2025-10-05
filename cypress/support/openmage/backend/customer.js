const defaultConfig = {
    _id_parent: '#nav-admin-customer',
    _h3: 'h3.icon-head',
}

cy.testBackendCustomer = {
    customer: {
        _id: '#nav-admin-customer-manage',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Customers',
            url: 'customer/index',
            _grid: '#customerGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Customer"]',
            },
        },
        edit: {
            title: 'John Smith',
            url: 'customer/edit',
        },
        new: {
            title: 'New Customer',
            url: 'customer/new',
        },
    },
    groups: {
        _id: '#nav-admin-customer-group',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Customer Groups',
            url: 'customer_group/index',
            _grid: '#customerGroupGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Customer Group"]',
            },
        },
        edit: {
            title: 'Edit Customer Group',
            url: 'customer_group/edit',
        },
        new: {
            title: 'New Customer Group',
            url: 'customer_group/new',
        },
    },
    online: {
        _id: '#nav-admin-customer-online',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Online Customers',
            url: 'customer_online/index',
        },
    },
}
