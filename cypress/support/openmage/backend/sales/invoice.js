const defaultConfig = {
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
}

cy.testBackendSalesInvoice = {
    config: {
        _id: '#nav-admin-sales-invoice',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Invoice',
            url: 'sales_invoice/index',
            _grid: '#sales_invoice_grid_table',
        },
        view: {
            title: 'Invoice #',
            url: 'sales_invoice/view',
        },
    },
}
