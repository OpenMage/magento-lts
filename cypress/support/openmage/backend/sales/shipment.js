const base = {
    _button: '.form-buttons button',
}

cy.testBackendSalesOrderShipment = {}

cy.testBackendSalesOrderShipment.config = {
    _id: '#nav-admin-sales-shipment',
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
    _button: base._button,
};

cy.testBackendSalesOrderShipment.config.index = {
    title: 'Shipments',
    url: 'sales_shipment/index',
    _grid: '#sales_shipment_grid_table',
}

cy.testBackendSalesOrderShipment.config.view = {
    title: 'Shipment #',
    url: 'sales_shipment/view',
    __buttons: {
        print: base._button + '[title="Print"]',
        tracking: base._button + '[title="Send Tracking Information"]',
        back: base._button + '[title="Back"]',
    },
}
