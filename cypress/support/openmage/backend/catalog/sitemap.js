const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
};

base.__fields = {
    sitemap_filename : {
        selector: '#sitemap_filename',
    },
    sitemap_path : {
        selector: '#sitemap_path',
    },
    page_store_id : {
        selector: '#store_id',
    },
};

cy.testBackendCatalogSitemap = {};

cy.testBackendCatalogSitemap.config = {
    _id: '#nav-admin-catalog-sitemap',
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCatalogSitemap.config.index = {
    title: 'Google Sitemap',
    url: 'sitemap/index',
    _grid: '#sitemapGrid_table',
    __buttons: {
        add: base._button + '[title="Add Sitemap"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendCatalogSitemap.config.index.__buttons.add, 'Add New Sitemap button clicked');
    },
}

cy.testBackendCatalogSitemap.config.edit = {
    title: 'Edit Sitemap',
    url: 'sitemap/edit',
    __fields: base.__fields,
    __buttons: {
        save: base._button + '[title="Save"]',
        delete: base._button + '[title="Delete"]',
        generate: base._button + '[title="Save & Generate"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendCatalogSitemap.config.edit.__buttons.save, 'Save button clicked');
    },
    clickSaveAndGenerate: () => {
        tools.click(cy.testBackendCatalogSitemap.config.edit.__buttons.generate, 'Save & Generate button clicked');
    },
    clickDelete: () => {
        tools.click(cy.testBackendCatalogSitemap.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCatalogSitemap.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCatalogSitemap.config.edit.__buttons.reset, 'Reset button clicked');
    },
}

cy.testBackendCatalogSitemap.config.new = {
    title: 'New Sitemap',
    url: 'sitemap/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        generate: base._button + '[title="Save & Generate"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendCatalogSitemap.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndGenerate: () => {
        tools.click(cy.testBackendCatalogSitemap.config.new.__buttons.generate, 'Save & Generate button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCatalogSitemap.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCatalogSitemap.config.new.__buttons.reset, 'Reset button clicked');
    },
}
