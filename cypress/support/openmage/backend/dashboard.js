const defaulfConfig = {
    _id_parent: '#nav-admin-dashboard',
    _h3: 'h3.head-dashboard',
}

cy.testBackendDashboard = {
    dashboard: {
        _id: '#nav-admin-dashboard',
        _id_parent: defaulfConfig._id_parent,
        _h3: defaulfConfig._h3,
        index: {
            title: 'Dashboard',
            url: 'dashboard/index',
        },
    },
}
