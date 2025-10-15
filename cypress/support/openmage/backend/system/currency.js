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
    url: 'system_currency/index',
    index: {},
}

/**
 * Configuration for "Manage Currency Rates" page
 * @type {{__buttons: {import: string, save: string, reset: string}, __validation: {_input: {from: string}}, title: string, url: string}}
 */
test.config.index = {
    title: 'Manage Currency Rates',
    url: test.config.url,
    __buttons: {
        save: {
            _: base._button + '[title="Save Currency Rates"]',
            __class: base.__buttons.save.__class,
        },
        import: {
            _: base._button + '[title="Import"]',
            __class: ['scalable', 'add', 'import'],
        },
        reset: base.__buttons.reset,
    },
    __validation: {
        _input: {
            from: 'input[name="rate[USD][EUR]"]',
        }
    }
}
