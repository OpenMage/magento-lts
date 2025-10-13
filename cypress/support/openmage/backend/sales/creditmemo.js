const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.creditmemo;

/**
 * Configuration for "Credit Memos" in the Admin menu
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, view: {}}}
 */
test.config = {
    _: '#nav-admin-sales-creditmemo',
    _nav: '#nav-admin-sales',
    _title: base._title,
    _button: base._button,
    url: 'sales_creditmemo/index',
    index: {},
    view: {},
};

/**
 * Configuration for "Credit Memos" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Credit Memos',
    url: test.config.url,
    _grid: '#sales_creditmemo_grid_table',
}

/**
 * Configuration for "View Credit Memo" page
 * @type {{__buttons: {print: string, back: string, email: string}, title: string, url: string}}
 */
test.config.view = {
    title: 'Credit Memo #',
    url: 'sales_creditmemo/view',
    __buttons: {
        print: {
            _: base._button + '[title="Print"]',
        },
        email: {
            _: base._button + '[title="Send Email"]',
        },
        back: {
            _: base.__buttons.back._,
        },
    },
}
