const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.promo.catalog;
const tools = cy.openmage.tools;

/**
 * Configuration for "Catalog Price Rules" menu item
 * @type {{_id_parent: string, _id: string, _title: string, _button: string, url: string, new: {}, edit: {}, index: {}}}
 */
test.config = {
    _id: '#nav-admin-promo-catalog',
    _id_parent: '#nav-admin-promo',
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
        },
        apply: {
            _: base._button + '[title="Apply Rules"]',
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
        save: {
            _: base.__buttons.save._,
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
        },
        saveAndApply: {
            _: base._button + '[title="Save and Apply"]',
        },
        delete: {
            _: base.__buttons.delete._,
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
    },
    clickSave: () => {
        base.__buttons.save.click();
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
    clickSaveAndApply: () => {
        tools.click(test.config.edit.__buttons.saveAndApply._, 'Save and Apply button clicked');
    },
    clickDelete: () => {
        base.__buttons.delete.click();
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
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
        save: {
            _: base.__buttons.save._,
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
        },
        saveAndApply: {
            _: base._button + '[title="Save and Apply"]',
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
    },
    clickSave: () => {
        base.__buttons.save.click();
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
    clickSaveAndApply: () => {
        tools.click(test.config.new.__buttons.saveAndApply._, 'Save and Apply button clicked');
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
    },
}
