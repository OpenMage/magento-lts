const defaultConfig = {
    _id_parent: '#nav-admin-promo',
    _h3: 'h3.icon-head',
}

cy.testBackendPromoCatalog = {
    config: {
        _id: '#nav-admin-promo-catalog',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Catalog Price Rules',
            url: 'promo_catalog/index',
            _grid: '#promo_catalog_grid',
            __buttons: {
                add: '.form-buttons button[title="Add New Rule"]',
            },
        },
        edit: {
            title: 'Edit Rule',
            url: 'promo_catalog/edit',
        },
        new: {
            title: 'New Rule',
            url: 'promo_catalog/new',
        },
    },
}
