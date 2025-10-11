const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.cache;

/**
 * Configuration for "Cache Storage Management" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string, index: {}}}
 */
test.config = {
    _id: '#nav-admin-system-cache',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'cache/index',
    index: {},
}

/**
 * Configuration for "Cache Storage Management" page
 * @type {{__buttons: {flushApply: string, flushCache: string}, title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Cache Storage Management',
    url: test.config.url,
    _grid: '#cache_grid_table',
    __buttons: {
        flushApply: {
            _: base._button + '[title="Flush & Apply Updates"]',
        },
        flushCache: {
            _: base._button + '[title="Flush Cache Storage"]',
        },
    },
}
