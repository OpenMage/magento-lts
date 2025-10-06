const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemIndex = {
    config: {
        _id: '#nav-admin-system-index',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        list: {
            title: 'Index Management',
            url: 'process/list',
            _grid: '#indexer_processes_grid_table',
        },
    },
}
