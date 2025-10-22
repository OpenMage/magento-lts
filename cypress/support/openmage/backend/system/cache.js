const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.cache;

/**
 * Configuration for "Cache Storage Management" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-system-cache',
    _nav: '#nav-admin-system',
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
            __class: ['scalable', 'delete', 'cache'],
        },
        flushCache: {
            _: base._button + '[title="Flush Cache Storage"]',
            __class: ['scalable', 'delete', 'flush'],
        },
    },
}
