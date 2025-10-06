const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSalesInvoice = {
    config: {
        _id: '#nav-admin-sales-invoice',
        _id_parent: '#nav-admin-sales',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Invoice',
            url: 'sales_invoice/index',
            _grid: '#sales_invoice_grid_table',
        },
        view: {
            title: 'Invoice #',
            url: 'sales_invoice/view',
            __buttons: {
                print: defaultConfig._button + '[title="Print"]',
                email: defaultConfig._button + '[title="Send Email"]',
                back: defaultConfig._button + '[title="Back"]',
            },
        },
    },
}
