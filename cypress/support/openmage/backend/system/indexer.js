const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.indexer;

/**
 * Configuration for "Index Management" menu item
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-system-index',
    _nav: '#nav-admin-system',
    _title: base._title,
    url: 'admin/process/list',
    index: {},
}

/**
 * Configuration for "Index Management" page
 * @type {{title: string, url: string, grid: {}}}
 */
test.config.index = {
    title: 'Index Management',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'ended_at', dir: 'desc' } }},
}
