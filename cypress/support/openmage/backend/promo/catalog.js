const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.promo.catalog;
const tools = cy.openmage.tools;

/**
 * Configuration for "Catalog Price Rules" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-promo-catalog',
    _nav: '#nav-admin-promo',
    _title: base._title,
    _button: base._button,
    url: 'promo_catalog/index',
    index: {},
    edit: {},
    new: {},
};

/**
 * Configuration for "Catalog Price Rules" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Catalog Price Rules',
    url: test.config.url,
    _grid: '#promo_catalog_grid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Catalog Price Rules" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.promo.catalog.config.index.__buttons.add.click, _: string}, apply: {__class: string[], click: cy.openmage.test.backend.promo.catalog.config.index.__buttons.apply.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Rule"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Catalog Price Rule button clicked');
        },
    },
    apply: {
        _: base._button + '[title="Apply Rules"]',
        __class: ['scalable', 'apply'],
        click: () => {
            tools.click(test.config.index.__buttons.apply._, 'Apply Rules button clicked');
        },
    },
}

/**
 * Configuration for "Edit Rule" page
 * @type {{__buttons: {saveAndApply: string, save: string, back: string, reset: string, saveAndContinue: string, delete: string}, title: string, url: string}}
 */
test.config.edit = {
    title: 'Edit Rule',
    url: 'promo_catalog/edit',
    __buttons: {
        save: base.__buttons.save,
        saveAndContinue: base.__buttons.saveAndContinue,
        saveAndApply: base.__buttons.saveAndApply,
        delete: base.__buttons.delete,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
}

/**
 * Configuration for "New Rule" page
 * @type {{__buttons: {saveAndApply: string, save: string, back: string, reset: string, saveAndContinue: string}, title: string, url: string}}
 */
test.config.new = {
    title: 'New Rule',
    url: 'promo_catalog/new',
    __buttons: {
        save: base.__buttons.save,
        saveAndContinue: base.__buttons.saveAndContinue,
        saveAndApply: base.__buttons.saveAndApply,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
}
