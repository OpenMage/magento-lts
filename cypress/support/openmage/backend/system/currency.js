const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendSystemCurrencyRates = {
    config: {
        _id: '#nav-admin-system-currency-rates',
        _id_parent: '#nav-admin-system',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Manage Currency Rates',
            url: 'system_currency/index',
            __buttons: {
                save: defaultConfig._button + '[title="Save Currency Rates"]',
                import: defaultConfig._button + '[title="Import"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
            __validation: {
                _input: {
                    from: 'input[name="rate[USD][EUR]"]',
                }
            }
        },
    },
}
