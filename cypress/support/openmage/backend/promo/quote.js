const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.promo.quote;
const tools = cy.openmage.tools;

/**
 * Configuration for "Shopping Cart Price Rules" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-promo-quote',
    _id_parent: '#nav-admin-promo',
    _title: base._title,
    _button: base._button,
    url: 'promo_quote/index',
};

/**
 * Configuration for "Shopping Cart Price Rules" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.promo.quote.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Shopping Cart Price Rules',
    url: test.config.url,
    _grid: '#promo_quote_grid_table',
    __buttons: {
        add: base._button + '[title="Add New Rule"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Shopping Cart Price Rules button clicked');
    },
}

/**
 * Configuration for "Edit Rule" page
 * @type {{__buttons: *, title: string, url: string}}
 */
test.config.edit = {
    title: 'Edit Rule',
    url: 'promo_quote/edit',
    __buttons: base.__buttons,
}

/**
 * Configuration for "New Rule" page
 * @type {{__buttons: *, title: string, url: string}}
 */
test.config.new = {
    title: 'New Rule',
    url: 'promo_quote/new',
    __buttons: base.__buttonsNew,
}
