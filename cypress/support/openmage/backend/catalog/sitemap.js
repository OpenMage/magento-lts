const defaultConfig = {
    _button: '.form-buttons button'
}

cy.testBackendCatalogSitemap = {
    config: {
        _id: '#nav-admin-catalog-sitemap',
        _id_parent: '#nav-admin-catalog',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Google Sitemap',
            url: 'sitemap/index',
            _grid: '#sitemapGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add Sitemap"]',
            },
        },
        edit: {
            title: 'Edit Sitemap',
            url: 'sitemap/edit',
        },
        new: {
            title: 'New Sitemap',
            url: 'sitemap/new',
        },
    },
}
