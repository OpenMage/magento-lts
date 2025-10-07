const base = {
    _button: '.form-buttons button',
}

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
}

cy.testBackendSystemVariable.config.edit = {
    title: 'Custom Variable',
    url: 'system_variable/edit',
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
}
