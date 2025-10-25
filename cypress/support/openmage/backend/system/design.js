const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.design;
const tools = cy.openmage.tools;

/**
 * Selectors for fields on "New Design Change" and "Edit Design Change" pages
 * @type {{store_id: {_: string}, design: {_: string}, date_to: {_: string}, date_from: {_: string}}}
 * @private
 */
test.__fields = {
    store_id : {
        _: '#store_id',
    },
    design : {
        _: '#design',
    },
    date_from : {
        _: '#date_from',
    },
    date_to : {
        _: '#date_to',
    },
};

/**
 * Configuration for "Design" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-system-design',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_design/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Design" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Design',
    url: test.config.url,
    _grid: '#designGrid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Design" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.system.design.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add Design Change"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Design button clicked');
        },
    },
}

/**
 * Configuration for "Edit Design Change" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.editNoContinue, __fields: test.config.new.__fields}}
 */
test.config.edit = {
    title: 'Edit Design Change',
    url: 'system_design/edit',
    __buttons: base.__buttonsSets.editNoContinue,
    __fields: test.__fields,
}

/**
 * Configuration for "New Design Change" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.newNoContinue, __fields: test.config.new.__fields}}
 */
test.config.new = {
    title: 'New Design Change',
    url: 'system_design/new',
    __buttons: base.__buttonsSets.newNoContinue,
    __fields: test.__fields,
}
