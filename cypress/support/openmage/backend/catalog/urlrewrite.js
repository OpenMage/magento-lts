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
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'URL Rewrite Management',
    url: test.config.url,
    _grid: '#urlrewriteGrid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "URL Rewrite Management" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.catalog.urlRewrite.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add URL Rewrite"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add URL Rewrite button clicked');
        },
    },
}

/**
 * Configuration for "Edit URL Rewrite" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.editNoContinue}}
 */
test.config.edit = {
    title: 'Edit URL Rewrite',
    url: 'urlrewrite/edit',
    __buttons: base.__buttonsSets.editNoContinue,
}

/**
 * Configuration for "Add New URL Rewrite" page
 * @type {{title: string, url: string, __buttons: {back: cy.openmage.test.backend.__base.__buttons.back}}}
 */
test.config.new = {
    title: 'Add New URL Rewrite',
    url: 'urlrewrite/edit',
    __buttons: {
        back: base.__buttons.back,
    },
}
