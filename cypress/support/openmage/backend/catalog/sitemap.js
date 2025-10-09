const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.sitemap;
const tools = cy.openmage.tools;

/**
 * Selectors for fields on "New Sitemap" and "Edit Sitemap" pages
 * @type {{sitemap_path: {selector: string}, page_store_id: {selector: string}, sitemap_filename: {selector: string}}}
 * @private
 */
test.__fields = {
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

/**
 * Configuration for "Google Sitemap" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-catalog-sitemap',
    _id_parent: '#nav-admin-catalog',
    _title: base._title,
    _button: base._button,
    url: 'sitemap/index',
}

/**
 * Configuration for "Google Sitemap" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.catalog.sitemap.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Google Sitemap',
    url: test.config.url,
    _grid: '#sitemapGrid_table',
    __buttons: {
        add: base._button + '[title="Add Sitemap"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Sitemap button clicked');
    },
}

/**
 * Configuration for "Edit Sitemap" page
 * @type {{clickReset: cy.openmage.test.backend.catalog.sitemap.config.edit.clickReset, __buttons: {save: string, back: string, reset: string, delete: string, generate: string}, clickBack: cy.openmage.test.backend.catalog.sitemap.config.edit.clickBack, clickSave: cy.openmage.test.backend.catalog.sitemap.config.edit.clickSave, clickDelete: cy.openmage.test.backend.catalog.sitemap.config.edit.clickDelete, title: string, __fields: *, clickSaveAndGenerate: cy.openmage.test.backend.catalog.sitemap.config.edit.clickSaveAndGenerate, url: string}}
 */
test.config.edit = {
    title: 'Edit Sitemap',
    url: 'sitemap/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        delete: base._button + '[title="Delete"]',
        generate: base._button + '[title="Save & Generate"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save, 'Save button clicked');
    },
    clickSaveAndGenerate: () => {
        tools.click(test.config.edit.__buttons.generate, 'Save & Generate button clicked');
    },
    clickDelete: () => {
        tools.click(test.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(test.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.edit.__buttons.reset, 'Reset button clicked');
    },
}

/**
 * Configuration for "New Sitemap" page
 * @type {{clickReset: cy.openmage.test.backend.catalog.sitemap.config.new.clickReset, __buttons: {save: string, back: string, reset: string, generate: string}, clickBack: cy.openmage.test.backend.catalog.sitemap.config.new.clickBack, clickSave: cy.openmage.test.backend.catalog.sitemap.config.new.clickSave, title: string, __fields: (*|{sitemap_path: {selector: string}, page_store_id: {selector: string}, sitemap_filename: {selector: string}}), clickSaveAndGenerate: cy.openmage.test.backend.catalog.sitemap.config.new.clickSaveAndGenerate, url: string}}
 */
test.config.new = {
    title: 'New Sitemap',
    url: 'sitemap/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        generate: base._button + '[title="Save & Generate"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndGenerate: () => {
        tools.click(test.config.new.__buttons.generate, 'Save & Generate button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset, 'Reset button clicked');
    },
}
