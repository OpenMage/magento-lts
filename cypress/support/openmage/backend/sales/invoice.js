const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.invoice;

/**
 * Configuration for "Invoices" in the Admin menu
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, view: {}}}
 */
test.config = {
    _: '#nav-admin-sales-invoice',
    _nav: '#nav-admin-sales',
    _title: base._title,
    _button: base._button,
    url: 'sales_invoice/index',
    index: {},
    view: {},
};

/**
 * Configuration for "Invoices" page
 * @type {{title: string, url: string, grid: {}}}
 */
test.config.index = {
    title: 'Invoice',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'created_at', dir: 'desc' } }},
}

/**
 * Configuration for "View Invoice" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.sales}}
 */
test.config.view = {
    title: 'Invoice #',
    url: 'sales_invoice/view',
    __buttons: base.__buttonsSets.sales,
}
