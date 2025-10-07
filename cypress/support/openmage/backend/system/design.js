const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.design;
const tools = cy.openmage.tools;

/**
 * Selectors for fields on "New Design Change" and "Edit Design Change" pages
 * @type {{store_id: {selector: string}, design: {selector: string}, date_to: {selector: string}, date_from: {selector: string}}}
 * @private
 */
test.__fields = {
    store_id : {
        selector: '#store_id',
    },
    design : {
        selector: '#design',
    },
    date_from : {
        selector: '#date_from',
    },
    date_to : {
        selector: '#date_to',
    },
};

/**
 * Configuration for "Design" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-design',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_design/index',
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
        add: base._button + '[title="Add Design Change"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Design button clicked');
    },
}

/**
 * Configuration for "Edit Design Change" page
 * @type {{__buttons: {save: string, back: string, delete: string}, clickBack: cy.openmage.test.backend.system.design.config.edit.clickBack, clickSave: cy.openmage.test.backend.system.design.config.edit.clickSave, clickDelete: cy.openmage.test.backend.system.design.config.edit.clickDelete, title: string, __fields: (*|{store_id: {selector: string}, design: {selector: string}, date_to: {selector: string}, date_from: {selector: string}}), url: string}}
 */
test.config.edit = {
    title: 'Edit Design Change',
    url: 'system_design/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickDelete: () => {
        tools.click(test.config.new.__buttons.generate, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
}

/**
 * Configuration for "New Design Change" page
 * @type {{__buttons: {save: string, back: string}, clickBack: cy.openmage.test.backend.system.design.config.new.clickBack, clickSave: cy.openmage.test.backend.system.design.config.new.clickSave, title: string, __fields: (*|{store_id: {selector: string}, design: {selector: string}, date_to: {selector: string}, date_from: {selector: string}}), url: string}}
 */
test.config.new = {
    title: 'New Design Change',
    url: 'system_design/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        back: base._button + '[title="Back"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
}
