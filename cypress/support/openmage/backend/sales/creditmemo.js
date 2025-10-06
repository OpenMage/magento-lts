const defaultConfig = {
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
}

cy.testBackendSalesCreditmemo = {
    config: {
        _id: '#nav-admin-sales-creditmemo',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Credit Memos',
            url: 'sales_creditmemo/index',
            _grid: '#sales_creditmemo_grid_table',
        },
        view: {
            title: 'Credit Memo #',
            url: 'sales_creditmemo/edit',
        },
    },
}
