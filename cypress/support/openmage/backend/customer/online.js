cy.testBackendCustomerOnline = {};

cy.testBackendCustomerOnline.config = {
    _id: '#nav-admin-customer-online',
    _id_parent: '#nav-admin-customer',
    _h3: 'h3.icon-head',
}

cy.testBackendCustomerOnline.config.index = {
    title: 'Online Customers',
    url: 'customer_online/index',
}
