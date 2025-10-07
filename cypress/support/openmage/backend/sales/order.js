const base = {
    _button: '.form-buttons button',
}

cy.testBackendSalesOrder = {};

cy.testBackendSalesOrder.config = {
    _id: '#nav-admin-sales-order',
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
    _button: base._button,
};

cy.testBackendSalesOrder.config.index = {
    title: 'Orders',
    url: 'sales_order/index',
    _grid: '#sales_order_grid_table',
    __buttons: {
        new: '.form-buttons button[title="Create New Order"]',
    },
}

cy.testBackendSalesOrder.config.view = {
    title: 'Order #',
    url: 'sales_order/view',
    __buttons: {
        reorder: base._button + '[title="Reorder"]',
        back: base._button + '[title="Back"]',
    },
}
