const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

base.__fields = {
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

cy.testBackendSystemVariable = {};

cy.testBackendSystemVariable.config = {
    _id: '#nav-admin-system-variable',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendSystemVariable.config.index = {
    title: 'Custom Variables',
    url: 'system_variable/index',
    _grid: '#customVariablesGrid',
    __buttons: {
        add: base._button + '[title="Add New Variable"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendSystemVariable.config.index.__buttons.add, 'Add New Custom Variable button clicked');
    },
}

cy.testBackendSystemVariable.config.edit = {
    title: 'Custom Variable',
    url: 'system_variable/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendSystemVariable.config.edit.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendSystemVariable.config.edit.__buttons.saveAndContinue, 'Save & Generate button clicked');
    },
    clickDelete: () => {
        tools.click(cy.testBackendSystemVariable.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendSystemVariable.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendSystemVariable.config.edit.__buttons.reset, 'Reset button clicked');
    },
}

cy.testBackendSystemVariable.config.new = {
    title: 'New Custom Variable',
    url: 'system_variable/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendSystemVariable.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendSystemVariable.config.new.__buttons.saveAndContinue, 'Save and Continue Edit button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendSystemVariable.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendSystemVariable.config.new.__buttons.reset, 'Reset button clicked');
    },
}
