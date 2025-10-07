const base = {
    _button: '.form-buttons button',
}

cy.testBackendSalesInvoice = {};

cy.testBackendSalesInvoice.config = {
    _id: '#nav-admin-sales-invoice',
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
    _button: base._button,
};

cy.testBackendSalesInvoice.config.index = {
    title: 'Invoice',
    url: 'sales_invoice/index',
    _grid: '#sales_invoice_grid_table',
}

cy.testBackendSalesInvoice.config.view = {
    title: 'Invoice #',
    url: 'sales_invoice/view',
    __buttons: {
        print: base._button + '[title="Print"]',
        email: base._button + '[title="Send Email"]',
        back: base._button + '[title="Back"]',
    },
}
