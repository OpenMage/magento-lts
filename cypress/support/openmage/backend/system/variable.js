const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.variable;
const tools = cy.openmage.tools;

/**
 * Selectors for fields in "Custom Variable" form
 * @type {{code: {selector: string}, plain_value: {selector: string}, name: {selector: string}, html_value: {selector: string}}}
 * @private
 */
test.__fields = {
    code : {
        selector: '#code',
    },
    name : {
        selector: '#name',
    },
    html_value : {
        selector: '#html_value',
    },
    plain_value : {
        selector: '#plain_value',
    },
};

/**
 * Configuration for "Custom Variables" section
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-variable',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_variable/index',
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
        add: base._button + '[title="Add New Variable"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Custom Variable button clicked');
    },
}

/**
 * Configuration for "Edit Custom Variable" page
 * @type {{clickReset: cy.openmage.test.backend.system.variable.config.edit.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string, delete: string}, clickBack: cy.openmage.test.backend.system.variable.config.edit.clickBack, clickSave: cy.openmage.test.backend.system.variable.config.edit.clickSave, clickDelete: cy.openmage.test.backend.system.variable.config.edit.clickDelete, title: string, __fields: *, clickSaveAndContinue: cy.openmage.test.backend.system.variable.config.edit.clickSaveAndContinue, url: string}}
 */
test.config.edit = {
    title: 'Custom Variable',
    url: 'system_variable/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(test.config.edit.__buttons.saveAndContinue, 'Save & Generate button clicked');
    },
    clickDelete: () => {
        tools.click(test.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(test.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.edit.__buttons.reset, 'Reset button clicked');
    },
}

/**
 * Configuration for "New Custom Variable" page
 * @type {{clickReset: cy.openmage.test.backend.system.variable.config.new.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string}, clickBack: cy.openmage.test.backend.system.variable.config.new.clickBack, clickSave: cy.openmage.test.backend.system.variable.config.new.clickSave, title: string, __fields: *, clickSaveAndContinue: cy.openmage.test.backend.system.variable.config.new.clickSaveAndContinue, url: string}}
 */
test.config.new = {
    title: 'New Custom Variable',
    url: 'system_variable/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(test.config.new.__buttons.saveAndContinue, 'Save and Continue Edit button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset, 'Reset button clicked');
    },
}
