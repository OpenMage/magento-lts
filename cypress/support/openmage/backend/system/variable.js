const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.variable;
const tools = cy.openmage.tools;

/**
 * Selectors for fields in "Custom Variable" form
 * @type {{code: {_: string}, plain_value: {_: string}, name: {_: string}, html_value: {_: string}}}
 * @private
 */
test.__fields = {
    code : {
        _: '#code',
    },
    name : {
        _: '#name',
    },
    html_value : {
        _: '#html_value',
    },
    plain_value : {
        _: '#plain_value',
    },
};

/**
 * Configuration for "Custom Variables" section
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-system-variable',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_variable/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Custom Variables" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Custom Variables',
    url: test.config.url,
    _grid: '#customVariablesGrid',
    __buttons: {},
}

/**
 * Buttons for "Custom Variables" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.system.variable.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Variable"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Custom Variable button clicked');
        },
    },
}

/**
 * Configuration for "Edit Custom Variable" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.edit, __fields: test.config.edit.__fields}}
 */
test.config.edit = {
    title: 'Custom Variable',
    url: 'system_variable/edit',
    __buttons: base.__buttonsSets.edit,
    __fields: test.__fields,
}

/**
 * Configuration for "New Custom Variable" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new, __fields: test.config.new.__fields}}
 */
test.config.new = {
    title: 'New Custom Variable',
    url: 'system_variable/new',
    __buttons: base.__buttonsSets.new,
    __fields: test.__fields,
}
