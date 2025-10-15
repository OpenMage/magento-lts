const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.search;
const tools = cy.openmage.tools;

/**
 * Configuration for fields in "Search Terms" edit and new pages
 * @type {query_text: {_: string}, store_id: {_: string}, synonym_for: {_: string}, page_is_active: {_: string}, display_in_terms: {_: string}}
 * @private
 */
test.__fields = {
    query_text : {
        _: '#query_text',
    },
    store_id : {
        _: '#store_id',
    },
    synonym_for : {
        _: '#synonym_for',
    },
    page_is_active : {
        _: '#redirect',
    },
    display_in_terms : {
        _: '#display_in_terms',
    },
};

/**
 * Configuration for "Search Terms" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-catalog-search',
    _nav: '#nav-admin-catalog',
    _title: base._title,
    _button: base._button,
    url: 'catalog_search/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Search Terms" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Search',
    url: test.config.url,
    _grid: '#catalog_search_grid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Search Terms" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.catalog.search.config.index.__buttons.add.click, _: string}}}
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Search Term"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add Search Term button clicked');
        },
    },
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
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.newNoContinue, __fields: test.config.new.__fields}}
 */
test.config.new = {
    title: 'New Search',
    url: 'catalog_search/new',
    __buttons: base.__buttonsSets.newNoContinue,
    __fields: test.__fields,
}
