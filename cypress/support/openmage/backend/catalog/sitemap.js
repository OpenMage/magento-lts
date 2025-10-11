const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.sitemap;
const tools = cy.openmage.tools;

/**
 * Selectors for fields on "New Sitemap" and "Edit Sitemap" pages
 * @type {{sitemap_filename: {_: string}, sitemap_path: {_: string}, page_store_id: {_: string}}}
 * @private
 */
test.__fields = {
    sitemap_filename : {
        _: '#sitemap_filename',
    },
    sitemap_path : {
        _: '#sitemap_path',
    },
    page_store_id : {
        _: '#store_id',
    },
};

/**
 * Configuration for "Google Sitemap" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-catalog-sitemap',
    _nav: '#nav-admin-catalog',
    _title: base._title,
    _button: base._button,
    url: 'sitemap/index',
    index: {},
    edit: {},
    new: {},
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
        add: {
            _: base._button + '[title="Add Sitemap"]',
            __class: base.__buttons.add.__class,
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Sitemap button clicked');
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
        save: base.__buttons.save,
        delete: base.__buttons.delete,
        generate: {
            _: base._button + '[title="Save & Generate"]',
            __class: ['scalable', 'add', 'add-generate'],
        },
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    __fields: test.__fields,
    clickSaveAndGenerate: () => {
        tools.click(test.config.edit.__buttons.generate._, 'Save & Generate button clicked');
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
        save: base.__buttons.save,
        generate: {
            _: base._button + '[title="Save & Generate"]',
            __class: ['scalable', 'add', 'add-generate'],
        },
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    __fields: test.__fields,
    clickSaveAndGenerate: () => {
        tools.click(test.config.new.__buttons.generate._, 'Save & Generate button clicked');
    },
}
