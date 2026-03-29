const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.design;
const tools = cy.openmage.tools;

/**
 * Configuration for "Design" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-system-design',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/system/design',
    url: 'admin/system_design',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Design" page
 * @type {{title: string, url: string, grid: {}, __buttons: {}}}
 */
test.config.index = {
    title: 'Design',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'package', dir: 'asc' } }},
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
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.editNoContinue}}
 */
test.config.edit = {
    title: 'Edit Design Change',
    url: 'system_design/edit',
    __buttons: base.__buttonsSets.editNoContinue,
}

/**
 * Configuration for "New Design Change" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.newNoContinue}}
 */
test.config.new = {
    title: 'New Design Change',
    url: 'system_design/new',
    __buttons: base.__buttonsSets.newNoContinue,
}
