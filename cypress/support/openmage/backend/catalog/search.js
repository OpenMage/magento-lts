const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.search;
const tools = cy.openmage.tools;

/**
 * Configuration for fields in "Search Terms" edit and new pages
 * @type {{store_id: {selector: string}, display_in_terms: {selector: string}, page_is_active: {selector: string}, synonym_for: {selector: string}, query_text: {selector: string}}}
 * @private
 */
test.__fields = {
    query_text : {
        selector: '#query_text',
    },
    store_id : {
        selector: '#store_id',
    },
    synonym_for : {
        selector: '#synonym_for',
    },
    page_is_active : {
        selector: '#redirect',
    },
    display_in_terms : {
        selector: '#display_in_terms',
    },
};

/**
 * Configuration for "Search Terms" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-catalog-search',
    _id_parent: '#nav-admin-catalog',
    _title: base._title,
    _button: base._button,
    url: 'catalog_search/index',
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
        add: base._button + '[title="Add New Search Term"]',
    },
    clickAdd: (log = 'Add Search Term button clicked') => {
        tools.click(test.config.index.__buttons.add, log);
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
        save: base._button + '[title="Save Search"]',
        delete: base._button + '[title="Delete Search"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    clickDelete: () => {
        tools.click(test.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(test.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.edit.__buttons.reset, 'Reset button clicked');
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
        save: base._button + '[title="Save Search"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset, 'Reset button clicked');
    },
}
