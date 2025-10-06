const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSalesCreditmemo = {
    config: {
        _id: '#nav-admin-sales-creditmemo',
        _id_parent: '#nav-admin-sales',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Credit Memos',
            url: 'sales_creditmemo/index',
            _grid: '#sales_creditmemo_grid_table',
        },
        view: {
            title: 'Credit Memo #',
            url: 'sales_creditmemo/view',
            __buttons: {
                print: defaultConfig._button + '[title="Print"]',
                email: defaultConfig._button + '[title="Send Email"]',
                back: defaultConfig._button + '[title="Back"]',
            },
        },
    },
}
