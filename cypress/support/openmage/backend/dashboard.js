cy.testBackendDashboard = {};

cy.testBackendDashboard.config = {
    _id: '#nav-admin-dashboard',
    _id_parent: '#nav-admin-dashboard',
    _h3: 'h3.head-dashboard',
}

cy.testBackendDashboard.config.index = {
    title: 'Dashboard',
    url: 'dashboard/index',
}
