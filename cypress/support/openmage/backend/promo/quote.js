const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendPromoQuote = {
    config: {
        _id: '#nav-admin-promo-quote',
        _id_parent: '#nav-admin-promo',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
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
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                delete: defaultConfig._button + '[title="Delete"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
        new: {
            title: 'New Rule',
            url: 'promo_quote/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
