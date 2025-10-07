const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.currency;

/**
 * Configuration for "Currency Rates" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-currency-rates',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_currency/index',
}

/**
 * Configuration for "Manage Currency Rates" page
 * @type {{__buttons: {import: string, save: string, reset: string}, __validation: {_input: {from: string}}, title: string, url: string}}
 */
test.config.index = {
    title: 'Manage Currency Rates',
    url: test.config.url,
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
