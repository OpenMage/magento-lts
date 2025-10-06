const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemCurrencyRates = {
    config: {
        _id: '#nav-admin-system-currency-rates',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Currency Rates',
            url: 'system_currency/index',
            __buttons: {
                save: '.form-buttons button[title="Save Currency Rates"]',
            },
            __validation: {
                _input: {
                    from: 'input[name="rate[USD][EUR]"]',
                }
            }
        },
    },
}
