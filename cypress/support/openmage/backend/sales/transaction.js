const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.transaction;

/**
 * Configuration for "Transactions" section
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-sales-transactions',
    _nav: '#nav-admin-sales',
    _title: base._title,
    title: '',
    url: 'sales_transactions/index',
    index: {},
};

/**
 * Configuration for "Transactions" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Transactions',
    url: test.config.url,
    _grid: '#order_transactions_table',
}
