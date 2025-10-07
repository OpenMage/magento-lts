const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.urlRewrite;
const tools = cy.openmage.tools;

/**
 * Configuration for "URL Rewrite" menu item
 * @type {{_id: string, _id_parent: string, _title: string, _button: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-catalog-urlrewrite',
    _id_parent: '#nav-admin-catalog',
    _title: base._title,
    _button: base._button,
    url: 'urlrewrite/index',
}

/**
 * Configuration for "URL Rewrite Management" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.cagtalog.urlRewrite.config.index.clickAdd}}
 */
test.config.index = {
    title: 'URL Rewrite Management',
    url: test.config.url,
    _grid: '#urlrewriteGrid_table',
    __buttons: {
        add: base._button + '[title="Add URL Rewrite"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add URL Rewrite button clicked');
    },
}

/**
 * Configuration for "Edit URL Rewrite" page
 * @type {{title: string, url: string, __buttons: {save: string, delete: string, back: string, reset: string}, clickSave: cy.openmage.test.backend.cagtalog.urlRewrite.config.edit.clickSave, clickDelete: cy.openmage.test.backend.cagtalog.urlRewrite.config.edit.clickDelete, clickBack: cy.openmage.test.backend.cagtalog.urlRewrite.config.edit.clickBack, clickReset: cy.openmage.test.backend.cagtalog.urlRewrite.config.edit.clickReset}}
 */
test.config.edit = {
    title: 'Edit URL Rewrite',
    url: 'urlrewrite/edit',
    __buttons: base.__buttonsNoContinue,
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save, 'Save button clicked');
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
 * Configuration for "Add New URL Rewrite" page
 * @type {{title: string, url: string, __buttons: {back: string}, clickBack: cy.openmage.test.backend.cagtalog.urlRewrite.config.new.clickBack}}
 */
test.config.new = {
    title: 'Add New URL Rewrite',
    url: 'urlrewrite/edit',
    __buttons: {
        back: base._button + '[title="Back"]',
    },
    clickBack: () => {
        tools.click(test.config.edit.__buttons.back, 'Back button clicked');
    },
}
