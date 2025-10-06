const defaultConfig = {
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
}

cy.testBackendCatalogSitemap = {
    config: {
        _id: '#nav-admin-catalog-sitemap',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Google Sitemap',
            url: 'sitemap/index',
            _grid: '#sitemapGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add Sitemap"]',
            },
        },
        edit: {
            title: 'Edit Sitemap',
            url: 'sitemap/edit',
        },
        new: {
            title: 'New Sitemap',
            url: 'sitemap/edit',
        },
    },
}
