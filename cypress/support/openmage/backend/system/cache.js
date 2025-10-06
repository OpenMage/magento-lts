const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSystemCache = {
    config: {
        _id: '#nav-admin-system-cache',
        _id_parent: '#nav-admin-system',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Cache Storage Management',
            url: 'cache/index',
            _grid: '#cache_grid_table',
            __buttons: {
                flushApply: defaultConfig._button + '[title="Flush & Apply Updates"]',
                flushCache: defaultConfig._button + '[title="Flush Cache Storage"]',
            },
        },
    },
}
