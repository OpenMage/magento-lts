const defaultConfig = {
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
}

cy.testBackendSalesOrderShipment = {
    config: {
        _id: '#nav-admin-sales-shipment',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Shipments',
            url: 'sales_shipment/index',
            _grid: '#sales_shipment_grid_table',
        },
        view: {
            title: 'Shipment #',
            url: 'sales_shipment/view',
        },
    },
}
