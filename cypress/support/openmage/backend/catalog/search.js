const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

base.__fields = {
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

cy.testBackendCatalogSearch = {};

cy.testBackendCatalogSearch.config = {
    _id: '#nav-admin-catalog-search',
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCatalogSearch.config.index = {
    title: 'Search',
    url: 'catalog_search/index',
    _grid: '#catalog_search_grid_table',
    __buttons: {
        add: base._button + '[title="Add New Search Term"]',
    },
    clickAdd: (log = 'Add Search Term button clicked') => {
        tools.click(cy.testBackendCatalogSearch.config.index.__buttons.add, log);
    },
}

cy.testBackendCatalogSearch.config.edit = {
    title: 'Edit Search',
    url: 'catalog_search/edit',
    __buttons: {
        save: base._button + '[title="Save Search"]',
        delete: base._button + '[title="Delete Search"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    clickDelete: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickSave: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.reset, 'Reset button clicked');
    },
}

cy.testBackendCatalogSearch.config.new = {
    title: 'New Search',
    url: 'catalog_search/new',
    __buttons: {
        save: base._button + '[title="Save Search"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendCatalogSearch.config.new.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCatalogSearch.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCatalogSearch.config.new.__buttons.reset, 'Reset button clicked');
    },
}
