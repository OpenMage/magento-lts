const base = {
    _button: '.form-buttons button',
}

cy.testBackendSalesCreditmemo = {};

cy.testBackendSalesCreditmemo.config = {
    _id: '#nav-admin-sales-creditmemo',
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
    _button: base._button,
};

cy.testBackendSalesCreditmemo.config.index = {
    title: 'Credit Memos',
    url: 'sales_creditmemo/index',
    _grid: '#sales_creditmemo_grid_table',
}

cy.testBackendSalesCreditmemo.config.view = {
    title: 'Credit Memo #',
    url: 'sales_creditmemo/view',
    __buttons: {
        print: base._button + '[title="Print"]',
        email: base._button + '[title="Send Email"]',
        back: base._button + '[title="Back"]',
    },
}
