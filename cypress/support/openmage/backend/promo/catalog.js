const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendPromoCatalog = {
    config: {
        _id: '#nav-admin-promo-catalog',
        _id_parent: '#nav-admin-promo',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Catalog Price Rules',
            url: 'promo_catalog/index',
            _grid: '#promo_catalog_grid',
            __buttons: {
                add: defaultConfig._button + '[title="Add New Rule"]',
                apply: defaultConfig._button + '[title="Apply Rules"]',
            },
        },
        edit: {
            title: 'Edit Rule',
            url: 'promo_catalog/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                saveAndApply: defaultConfig._button + '[title="Save and Apply"]',
                delete: defaultConfig._button + '[title="Delete"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
        new: {
            title: 'New Rule',
            url: 'promo_catalog/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                saveAndApply: defaultConfig._button + '[title="Save and Apply"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
