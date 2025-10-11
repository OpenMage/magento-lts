const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.transaction;

/**
 * Configuration for "Transactions" section
 * @type {{_title: string, _id: string, _id_parent: string, url: string, index: {}}}
 */
test.config = {
    _id: '#nav-admin-sales-transactions',
    _id_parent: '#nav-admin-sales',
    _title: base._title,
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
