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
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.system.variable.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Custom Variables',
    url: test.config.url,
    _grid: '#customVariablesGrid',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Variable"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Custom Variable button clicked');
    },
}

/**
 * Configuration for "Edit Custom Variable" page
 * @type {{clickReset: cy.openmage.test.backend.system.variable.config.edit.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string, delete: string}, clickBack: cy.openmage.test.backend.system.variable.config.edit.clickBack, clickSave: cy.openmage.test.backend.system.variable.config.edit.clickSave, clickDelete: cy.openmage.test.backend.system.variable.config.edit.clickDelete, title: string, __fields: *, clickSaveAndContinue: cy.openmage.test.backend.system.variable.config.edit.clickSaveAndContinue, url: string}}
 */
test.config.edit = {
    title: 'Custom Variable',
    url: 'system_variable/edit',
    __buttons: base.__buttons,
    __fields: test.__fields,
    clickSave: () => {
        base.__buttons.save.click();
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
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
 * Configuration for "New Custom Variable" page
 * @type {{clickReset: cy.openmage.test.backend.system.variable.config.new.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string}, clickBack: cy.openmage.test.backend.system.variable.config.new.clickBack, clickSave: cy.openmage.test.backend.system.variable.config.new.clickSave, title: string, __fields: *, clickSaveAndContinue: cy.openmage.test.backend.system.variable.config.new.clickSaveAndContinue, url: string}}
 */
test.config.new = {
    title: 'New Custom Variable',
    url: 'system_variable/new',
    __buttons: base.__buttonsNew,
    __fields: test.__fields,
    clickSave: () => {
        base.__buttons.save.click();
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
    },
}
