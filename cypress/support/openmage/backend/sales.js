const defaultConfig = {
    _id_parent: '#nav-admin-sales',
    _h3: 'h3.icon-head',
}

cy.testBackendSales = {
    creditmemo: {
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
    invoice: {
        _id: '#nav-admin-sales-invoice',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Invoice',
            url: 'sales_invoice/index',
            _grid: '#sales_invoice_grid_table',
        },
        view: {
            title: 'Invoice #',
            url: 'sales_invoice/view',
        },
    },
    order: {
        _id: '#nav-admin-sales-order',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Orders',
            url: 'sales_order/index',
            _grid: '#sales_order_grid_table',
            __buttons: {
                new: '.form-buttons button[title="Create New Order"]',
            },
        },
        view: {
            title: 'Order #',
            url: 'sales_order/view',
        },
    },
    shipment: {
        _id: '#nav-admin-sales-shipment',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Shipments',
            url: 'sales_shipment/index',
            _grid: '#sales_shipment_grid_table',
        },
        view: {
            title: 'Shipment #',
            url: 'sales_shipment/view',
        },
    },
    transactions: {
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
