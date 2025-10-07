const base = {
    _button: '.form-buttons button',
}

cy.testBackendSystemCache = {};

cy.testBackendSystemCache.config = {
    _id: '#nav-admin-system-cache',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendSystemCache.config.index = {
    title: 'Cache Storage Management',
    url: 'cache/index',
    _grid: '#cache_grid_table',
    __buttons: {
        flushApply: base._button + '[title="Flush & Apply Updates"]',
        flushCache: base._button + '[title="Flush Cache Storage"]',
    },
}
