const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.search;
const tools = cy.openmage.tools;

/**
 * Configuration for "Search Terms" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-catalog-search',
    _nav: '#nav-admin-catalog',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/catalog/search',
    url: 'admin/catalog_search',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Search Terms" page
 * @type {{title: string, url: string, grid: {}}}
 */
test.config.index = {
    title: 'Search',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'query_id', dir: 'asc' } }},
}

/**
 * Configuration for "Edit Search Term" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.editNoContinue}}
 */
test.config.edit = {
    title: 'Edit Search',
    url: 'catalog_search/edit',
    __buttons: base.__buttonsSets.editNoContinue,
}

/**
 * Configuration for "New Search Term" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.newNoContinue}}
 */
test.config.new = {
    title: 'New Search',
    url: 'catalog_search/new',
    __buttons: base.__buttonsSets.newNoContinue,
}
