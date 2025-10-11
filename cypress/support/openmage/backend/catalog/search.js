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
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.catalog.search.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Search',
    url: test.config.url,
    _grid: '#catalog_search_grid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Search Term"]',
            __class: base.__buttons.add.__class,
        },
    },
    clickAdd: (log = 'Add Search Term button clicked') => {
        tools.click(test.config.index.__buttons.add._, log);
    },
}

/**
 * Configuration for "Edit Search Term" page
 * @type {{clickReset: cy.openmage.test.backend.catalog.search.config.edit.clickReset, __buttons: {save: string, back: string, reset: string, delete: string}, clickBack: cy.openmage.test.backend.catalog.search.config.edit.clickBack, clickDelete: cy.openmage.test.backend.catalog.search.config.edit.clickDelete, clickSave: cy.openmage.test.backend.catalog.search.config.edit.clickSave, title: string, url: string}}
 */
test.config.edit = {
    title: 'Edit Search',
    url: 'catalog_search/edit',
    __buttons: {
        save: {
            _: base._button + '[title="Save Search"]',
            __class: base.__buttons.save.__class,
        },
        delete: {
            _: base._button + '[title="Delete Search"]',
            __class: base.__buttons.delete.__class,
        },
        back: {
            _: base.__buttons.back._,
            __class: base.__buttons.back.__class,
        },
        reset: {
            _: base.__buttons.reset._,
            __class: base.__buttons.reset.__class,
        },
    },
    clickDelete: () => {
        tools.click(test.config.edit.__buttons.delete._, 'Delete button clicked');
    },
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save._, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(test.config.edit.__buttons.back._, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.edit.__buttons.reset._, 'Reset button clicked');
    },
}

/**
 * Configuration for "New Search Term" page
 * @type {{clickReset: cy.openmage.test.backend.catalog.search.config.new.clickReset, __buttons: {save: string, back: string, reset: string}, clickBack: cy.openmage.test.backend.catalog.search.config.new.clickBack, clickSave: cy.openmage.test.backend.catalog.search.config.new.clickSave, title: string, __fields, url: string}}
 */
test.config.new = {
    title: 'New Search',
    url: 'catalog_search/new',
    __buttons: {
        save: {
            _: base._button + '[title="Save Search"]',
            __class: base.__buttons.save.__class,
        },
        back: {
            _: base.__buttons.back._,
            __class: base.__buttons.back.__class,
        },
        reset: {
            _: base.__buttons.reset._,
            __class: base.__buttons.reset.__class,
        },
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save._, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back._, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset._, 'Reset button clicked');
    },
}
