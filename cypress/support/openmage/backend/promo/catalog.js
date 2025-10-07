const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.promo.catalog;
const tools = cy.openmage.tools;

/**
 * Configuration for "Catalog Price Rules" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-promo-catalog',
    _id_parent: '#nav-admin-promo',
    _title: base._title,
    _button: base._button,
    url: 'promo_catalog/index',
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
        add: base._button + '[title="Add New Rule"]',
        apply: base._button + '[title="Apply Rules"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Catalog Price Rule button clicked');
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
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        saveAndApply: base._button + '[title="Save and Apply"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
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
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        saveAndApply: base._button + '[title="Save and Apply"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}
