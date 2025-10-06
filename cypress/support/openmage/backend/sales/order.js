const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSalesOrder = {
    config: {
        _id: '#nav-admin-sales-order',
        _id_parent: '#nav-admin-sales',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Orders',
            url: 'sales_order/index',
            _grid: '#sales_order_grid_table',
            __buttons: {
                new: '.form-buttons button[title="Create New Order"]',
            },
        },
        view: {
            title: 'Order #',
            url: 'sales_order/view',
            __buttons: {
                reorder: defaultConfig._button + '[title="Reorder"]',
                back: defaultConfig._button + '[title="Back"]',
            },
        },
    },
}
