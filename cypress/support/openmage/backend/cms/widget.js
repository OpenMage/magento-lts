const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.cms.widget;
const tools = cy.openmage.tools;

/**
 * Configuration for "Widget Instance" section
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-cms-widget_instance',
    _nav: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    url: 'widget_instance/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Manage Widget Instances" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Manage Widget Instances',
    url: test.config.url,
    _grid: '#widgetInstanceGrid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Manage Widget Instances" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.cms.widget.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Widget Instance"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Widget Instances button clicked');
        },
    },
}

/**
 * Configuration for "Edit Widget Instance" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.edit}}
 */
test.config.edit = {
    title: 'Widget',
    url: 'widget_instance/edit',
    __buttons: base.__buttonsSets.edit,
}

/**
 * Configuration for "New Widget Instance" page
 * @type {{__buttons: {back: string, reset: string}, title: string, url: string}}
 */
test.config.new = {
    title: 'New Widget Instance',
    url: 'widget_instance/new',
    __buttons: {
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
}
