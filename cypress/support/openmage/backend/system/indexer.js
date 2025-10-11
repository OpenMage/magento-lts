const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.indexer;

/**
 * Configuration for "Index Management" menu item
 * @type {{_title: string, _id: string, _id_parent: string, url: string, index: {}}}
 */
test.config = {
    _id: '#nav-admin-system-index',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    url: 'process/list',
    index: {},
}

/**
 * Configuration for "Index Management" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Index Management',
    url: test.config.url,
    _grid: '#indexer_processes_grid_table',
}
