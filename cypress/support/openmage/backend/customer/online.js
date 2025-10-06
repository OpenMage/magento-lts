const defaultConfig = {
    _id_parent: '#nav-admin-customer',
    _h3: 'h3.icon-head',
}

cy.testBackendCustomerOnline = {
    config: {
        _id: '#nav-admin-customer-online',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Online Customers',
            url: 'customer_online/index',
        },
    },
}
