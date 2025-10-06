const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSalesOrderShipment = {
    config: {
        _id: '#nav-admin-sales-shipment',
        _id_parent: '#nav-admin-sales',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Shipments',
            url: 'sales_shipment/index',
            _grid: '#sales_shipment_grid_table',
        },
        view: {
            title: 'Shipment #',
            url: 'sales_shipment/view',
            __buttons: {
                print: defaultConfig._button + '[title="Print"]',
                tracking: defaultConfig._button + '[title="Send Tracking Information"]',
                back: defaultConfig._button + '[title="Back"]',
            },
        },
    },
}
