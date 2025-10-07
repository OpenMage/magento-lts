cy.testBackendSalesTransactions = {};

cy.testBackendSalesTransactions.config = {
    _id: '#nav-admin-sales-transactions',
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
};

cy.testBackendSalesTransactions.config.index = {
    title: 'Transactions',
    url: 'sales_transactions/index',
    _grid: '#order_transactions_table',
}
