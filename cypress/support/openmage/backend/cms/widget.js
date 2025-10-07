const base = {
    _button: '.form-buttons button',
}

cy.testBackendCmsWidget = {};

cy.testBackendCmsWidget.config = {
    _id: '#nav-admin-cms-widget_instance',
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCmsWidget.config.index = {
    title: 'Manage Widget Instances',
    url: 'widget_instance/index',
    _grid: '#widgetInstanceGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Widget Instance"]',
    },
}

cy.testBackendCmsWidget.config.edit = {
    title: 'Widget',
    url: 'widget_instance/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}

cy.testBackendCmsWidget.config.new = {
    title: 'New Widget Instance',
    url: 'widget_instance/new',
    __buttons: {
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}
