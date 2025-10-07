cy.testBackendSystemIndex = {};

cy.testBackendSystemIndex.config = {
    _id: '#nav-admin-system-index',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemIndex.config.index = {
    title: 'Index Management',
    url: 'process/list',
    _grid: '#indexer_processes_grid_table',
}
