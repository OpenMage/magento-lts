const defaultConfig = {
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
}

cy.testBackendSalesTransactions = {
    config: {
        _id: '#nav-admin-sales-transactions',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Transactions',
            url: 'sales_transactions/index',
            _grid: '#order_transactions_table',
        },
    },
}
