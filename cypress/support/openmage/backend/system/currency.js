const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.currency;

/**
 * Configuration for "Currency Rates" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-system-currency-rates',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'admin/system_currency',
    index: {},
}

/**
 * Configuration for "Manage Currency Rates" page
 * @type {{__validation: {_input: {from: string}}, title: string, url: string}}
 */
test.config.index = {
    title: 'Manage Currency Rates',
    url: test.config.url,
    __validation: {
        _input: {
            from: 'input[name="rate[USD][EUR]"]',
        }
    }
}
