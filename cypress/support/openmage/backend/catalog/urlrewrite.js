const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.urlRewrite;
const tools = cy.openmage.tools;

/**
 * Configuration for "URL Rewrite" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-catalog-urlrewrite',
    _nav: '#nav-admin-catalog',
    _title: base._title,
    _button: base._button,
    url: 'urlrewrite/index',
    index: {},
    edit: {},
    new: {},
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
        add: {
            _: base._button + '[title="Add URL Rewrite"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add URL Rewrite button clicked');
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
        base.__buttons.save.click();
    },
    clickDelete: () => {
        base.__buttons.delete.click();
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
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
        back: {
            _: base.__buttons.back._,
            __class: base.__buttons.back.__class,
        },
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
}
