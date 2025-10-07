const base = {
    _button: '.form-buttons button',
}

cy.testBackendSystemCurrencyRates = {};

cy.testBackendSystemCurrencyRates.config = {
    _id: '#nav-admin-system-currency-rates',
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendSystemCurrencyRates.config.index = {
    title: 'Manage Currency Rates',
    url: 'system_currency/index',
    __buttons: {
        save: base._button + '[title="Save Currency Rates"]',
        import: base._button + '[title="Import"]',
        reset: base._button + '[title="Reset"]',
    },
    __validation: {
        _input: {
            from: 'input[name="rate[USD][EUR]"]',
        }
    }
}
