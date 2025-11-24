const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.promo.quote;
const tools = cy.openmage.tools;

/**
 * Configuration for "Shopping Cart Price Rules" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-promo-quote',
    _nav: '#nav-admin-promo',
    _title: base._title,
    _button: base._button,
    url: 'promo_quote/index',
    index: {},
    edit: {},
    new: {},
};

/**
 * Configuration for "Shopping Cart Price Rules" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Shopping Cart Price Rules',
    url: test.config.url,
    _grid: '#promo_quote_grid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Shopping Cart Price Rules" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.promo.quote.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Rule"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Shopping Cart Price Rules button clicked');
        },
    },
}

/**
 * Configuration for "Edit Rule" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.edit}}
 */
test.config.edit = {
    title: 'Edit Rule',
    url: 'promo_quote/edit',
    __buttons: base.__buttonsSets.edit,
}

/**
 * Configuration for "New Rule" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new}}
 */
test.config.new = {
    title: 'New Rule',
    url: 'promo_quote/new',
    __buttons: base.__buttonsSets.new,
}
