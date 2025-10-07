const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

base.__fields = {
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
    clickAdd: () => {
        tools.click(cy.testBackendSystemDesign.config.index.__buttons.add, 'Add New Design button clicked');
    },
}

cy.testBackendSystemDesign.config.edit = {
    title: 'Edit Design Change',
    url: 'system_design/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendSystemDesign.config.new.__buttons.save, 'Save button clicked');
    },
    clickDelete: () => {
        tools.click(cy.testBackendSystemDesign.config.new.__buttons.generate, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendSystemDesign.config.new.__buttons.back, 'Back button clicked');
    },
}

cy.testBackendSystemDesign.config.new = {
    title: 'New Design Change',
    url: 'system_design/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        back: base._button + '[title="Back"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendSystemDesign.config.new.__buttons.save, 'Save button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendSystemDesign.config.new.__buttons.back, 'Back button clicked');
    },
}
