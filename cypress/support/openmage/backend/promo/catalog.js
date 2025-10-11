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
 * @type {{__buttons: {add: string, apply: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.promo.catalog.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Catalog Price Rules',
    url: test.config.url,
    _grid: '#promo_catalog_grid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Rule"]',
            __class: base.__buttons.add.__class,
        },
        apply: {
            _: base._button + '[title="Apply Rules"]',
            __class: ['scalable', 'apply'],
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Catalog Price Rule button clicked');
    },
    clickApply: () => {
        tools.click(test.config.index.__buttons.apply._, 'Apply Rules button clicked');
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
        saveAndApply: {
            _: base._button + '[title="Save and Apply"]',
            __class: ['scalable', 'apply'],
        },
        delete: base.__buttons.delete,
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    clickSaveAndApply: () => {
        tools.click(test.config.edit.__buttons.saveAndApply._, 'Save and Apply button clicked');
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
        saveAndApply: {
            _: base._button + '[title="Save and Apply"]',
            __class: ['scalable', 'apply'],
        },
        back: base.__buttons.back,
        reset: base.__buttons.reset,
    },
    clickSaveAndApply: () => {
        tools.click(test.config.new.__buttons.saveAndApply._, 'Save and Apply button clicked');
    },
}
