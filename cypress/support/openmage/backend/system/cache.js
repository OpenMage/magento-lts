const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemCache = {
    config: {
        _id: '#nav-admin-system-cache',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Cache Storage Management',
            url: 'cache/index',
            _grid: '#cache_grid_table',
            __buttons: {
                flushApply: '.form-buttons button[title="Flush & Apply Updates"]',
                flushCache: '.form-buttons button[title="Flush Cache Storage"]',
            },
        },
    },
}
