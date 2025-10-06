const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendCmsWidget = {
    config: {
        _id: '#nav-admin-cms-widget_instance',
        _id_parent: '#nav-admin-cms',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Manage Widget Instances',
            url: 'widget_instance/index',
            _grid: '#widgetInstanceGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add New Widget Instance"]',
            },
        },
        edit: {
            title: 'Widget',
            url: 'widget_instance/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                delete: defaultConfig._button + '[title="Delete"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
        new: {
            title: 'New Widget Instance',
            url: 'widget_instance/new',
            __buttons: {
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    }
}
