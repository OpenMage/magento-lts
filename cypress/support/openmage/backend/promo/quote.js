const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.promo.quote;
const tools = cy.openmage.tools;

/**
 * Configuration for "Shopping Cart Price Rules" menu item
 * @type {{_id_parent: string, _id: string, _title: string, _button: string, url: string, new: {}, edit: {}, index: {}}}
 */
test.config = {
    _id: '#nav-admin-promo-quote',
    _id_parent: '#nav-admin-promo',
    _title: base._title,
    _button: base._button,
    url: 'promo_quote/index',
    index: {},
    edit: {},
    new: {},
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
        add: {
            _: base._button + '[title="Add New Rule"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Shopping Cart Price Rules button clicked');
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
    clickSave: () => {
        base.__buttons.save.click();
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
}

/**
 * Configuration for "New Rule" page
 * @type {{__buttons: *, title: string, url: string}}
 */
test.config.new = {
    title: 'New Rule',
    url: 'promo_quote/new',
    __buttons: base.__buttonsNew,
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
}
