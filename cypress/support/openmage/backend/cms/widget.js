const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.cms.widget;
const tools = cy.openmage.tools;

/**
 * Configuration for "Widget Instance" section
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _id: '#nav-admin-cms-widget_instance',
    _id_parent: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    url: 'widget_instance/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Manage Widget Instances" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.cms.widget.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Manage Widget Instances',
    url: test.config.url,
    _grid: '#widgetInstanceGrid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Widget Instance"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Widget Instances button clicked');
    },
}

/**
 * Configuration for "Edit Widget Instance" page
 * @type {{__buttons: {save: string, back: string, reset: string, saveAndContinue: string, delete: string}, title: string, url: string}}
 */
test.config.edit = {
    title: 'Widget',
    url: 'widget_instance/edit',
    __buttons: base.__buttons,
}

/**
 * Configuration for "New Widget Instance" page
 * @type {{__buttons: {back: string, reset: string}, title: string, url: string}}
 */
test.config.new = {
    title: 'New Widget Instance',
    url: 'widget_instance/new',
    __buttons: {
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
    },
}
