const base = {
    _button: '.form-buttons button'
}

cy.testBackendCatalogSitemap = {};

cy.testBackendCatalogSitemap.config = {
    _id: '#nav-admin-catalog-sitemap',
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCatalogSitemap.config.index = {
    title: 'Google Sitemap',
    url: 'sitemap/index',
    _grid: '#sitemapGrid_table',
    __buttons: {
        add: base._button + '[title="Add Sitemap"]',
    },
}

cy.testBackendCatalogSitemap.config.edit = {
    title: 'Edit Sitemap',
    url: 'sitemap/edit',
}

cy.testBackendCatalogSitemap.config.new = {
    title: 'New Sitemap',
    url: 'sitemap/new',
}
