const defaultConfig = {
    _id_parent: '#nav-admin-promo',
    _h3: 'h3.icon-head',
}

cy.testBackendPromoQuote = {
    config: {
        _id: '#nav-admin-promo-quote',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Shopping Cart Price Rules',
            url: 'promo_quote/index',
            _grid: '#promo_quote_grid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Rule"]',
            },
        },
        edit: {
            title: 'Edit Rule',
            url: 'promo_quote/edit',
        },
        new: {
            title: 'New Rule',
            url: 'promo_quote/new',
        },
    },
}
