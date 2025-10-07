const base = {
    _button: '.form-buttons button',
}

cy.testBackendSystemDesign = {};

cy.testBackendSystemDesign.config = {
    _id: '#nav-admin-system-design',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendSystemDesign.config.index = {
    title: 'Design',
    url: 'system_design/index',
    _grid: '#designGrid_table',
    __buttons: {
        add: base._button + '[title="Add Design Change"]',
    },
}

cy.testBackendSystemDesign.config.edit = {
    title: 'Edit Design Change',
    url: 'system_design/edit',
}

cy.testBackendSystemDesign.config.new = {
    title: 'New Design Change',
    url: 'system_design/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        back: base._button + '[title="Back"]',
    },
}
