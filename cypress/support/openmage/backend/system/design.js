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
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.system.design.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Design',
    url: test.config.url,
    _grid: '#designGrid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add Design Change"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Design button clicked');
    },
}

/**
 * Configuration for "Edit Design Change" page
 * @type {{__buttons: {save: string, back: string, delete: string}, clickBack: cy.openmage.test.backend.system.design.config.edit.clickBack, clickSave: cy.openmage.test.backend.system.design.config.edit.clickSave, clickDelete: cy.openmage.test.backend.system.design.config.edit.clickDelete, title: string, __fields: (*|{store_id: {_: string}, design: {_: string}, date_to: {_: string}, date_from: {_: string}}), url: string}}
 */
test.config.edit = {
    title: 'Edit Design Change',
    url: 'system_design/edit',
    __buttons: {
        save: {
            _: base.__buttons.save._,
        },
        delete: {
            _: base.__buttons.delete._,
        },
        back: {
            _: base.__buttons.back._,
        },
    },
    __fields: test.__fields,
    clickSave: () => {
        base.__buttons.save.click();
    },
    clickDelete: () => {
        base.__buttons.delete.click();
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
}

/**
 * Configuration for "New Design Change" page
 * @type {{__buttons: {save: string, back: string}, clickBack: cy.openmage.test.backend.system.design.config.new.clickBack, clickSave: cy.openmage.test.backend.system.design.config.new.clickSave, title: string, __fields: (*|{store_id: {_: string}, design: {_: string}, date_to: {_: string}, date_from: {_: string}}), url: string}}
 */
test.config.new = {
    title: 'New Design Change',
    url: 'system_design/new',
    __buttons: {
        save: {
            _: base.__buttons.save._,
        },
        back: {
            _: base.__buttons.back._,
        },
    },
    __fields: test.__fields,
    clickSave: () => {
        base.__buttons.save.click();
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
}
