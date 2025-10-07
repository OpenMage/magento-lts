const base = {
    _button: '.form-buttons button',
}

cy.testBackendPromoCatalog = {};

cy.testBackendPromoCatalog.config = {
    _id: '#nav-admin-promo-catalog',
    _id_parent: '#nav-admin-promo',
    _h3: 'h3.icon-head',
    _button: base._button,
};

cy.testBackendPromoCatalog.config.index = {
    title: 'Catalog Price Rules',
    url: 'promo_catalog/index',
    _grid: '#promo_catalog_grid',
    __buttons: {
        add: base._button + '[title="Add New Rule"]',
        apply: base._button + '[title="Apply Rules"]',
    },
}

cy.testBackendPromoCatalog.config.edit = {
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

cy.testBackendPromoCatalog.config.new = {
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
