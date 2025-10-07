cy.testBackendSystemAdminnotification = {};

cy.testBackendSystemAdminnotification.config = {
    _id: '#nav-admin-system-adminnotification',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemAdminnotification.config.index = {
    title: 'Messages Inbox',
    url: 'notification/index',
    _grid: '#notificationGrid_table',
}
