const defaultConfig = {
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
}

cy.testBackendSalesOrder = {
    config: {
        _id: '#nav-admin-sales-order',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
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
        },
    },
}
