const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.invoice;

/**
 * Configuration for "Invoices" in the Admin menu
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-sales-invoice',
    _id_parent: '#nav-admin-sales',
    _title: base._title,
    _button: base._button,
    url: 'sales_invoice/index',
};

/**
 * Configuration for "Invoices" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Invoice',
    url: test.config.url,
    _grid: '#sales_invoice_grid_table',
}

/**
 * Configuration for "View Invoice" page
 * @type {{__buttons: {print: string, back: string, email: string}, title: string, url: string}}
 */
test.config.view = {
    title: 'Invoice #',
    url: 'sales_invoice/view',
    __buttons: {
        print: base._button + '[title="Print"]',
        email: base._button + '[title="Send Email"]',
        back: base._button + '[title="Back"]',
    },
}
