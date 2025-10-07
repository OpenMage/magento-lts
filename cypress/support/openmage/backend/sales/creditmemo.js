const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.creditmemo;

/**
 * Configuration for "Credit Memos" in the Admin menu
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-sales-creditmemo',
    _id_parent: '#nav-admin-sales',
    _title: base._title,
    _button: base._button,
    url: 'sales_creditmemo/index',
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
        print: base._button + '[title="Print"]',
        email: base._button + '[title="Send Email"]',
        back: base._button + '[title="Back"]',
    },
}
