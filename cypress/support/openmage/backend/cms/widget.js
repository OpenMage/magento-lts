const defaultConfig = {
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
}

cy.testBackendCmsWidget = {
    config: {
        _id: '#nav-admin-cms-widget_instance',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Widget Instances',
            url: 'widget_instance/index',
            _grid: '#widgetInstanceGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Widget Instance"]',
            },
        },
        edit: {
            title: 'Widget',
            url: 'widget_instance/edit',
        },
        new: {
            title: 'New Widget Instance',
            url: 'widget_instance/new',
        },
    }
}
