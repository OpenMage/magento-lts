const defaultConfig = {
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
}

cy.testBackendCatalog = {
    products: {
        _id: '#nav-admin-catalog-products',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Products',
            url: 'catalog_product/index',
            _grid: '#productGrid_table',
            __buttons: {
                add: 'button[title="Add Product"]',
            },
        },
        edit: {
            title: 'Plaid Cotton',
            url: 'catalog_product/edit',
        },
        new: {
            title: 'New Product',
            url: 'catalog_product/new',
        },
    },
    categories: {
        _id: '#nav-admin-catalog-categories',
        _id_parent: defaultConfig._id_parent,
        _h3: '#category-edit-container ' + defaultConfig._h3,
        index: {
            title: 'New Root Category',
            url: 'catalog_category/index',
        },
    },
    search: {
        _id: '#nav-admin-catalog-search',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Search',
            url: 'catalog_search/index',
            _grid: '#catalog_search_grid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Search Term"]',
            },
        },
        edit: {
            title: 'Edit Search',
            url: 'catalog_search/edit',
        },
        new: {
            title: 'New Search',
            url: 'catalog_search/new',
        },
    },
    sitemap: {
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
    urlrewrite: {
        _id: '#nav-admin-catalog-urlrewrite',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'URL Rewrite Management',
            url: 'urlrewrite/index',
            _grid: '#urlrewriteGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add URL Rewrite"]',
            },
        },
        edit: {
            title: 'Edit URL Rewrite',
            url: 'urlrewrite/edit',
        },
        new: {
            title: 'Add New URL Rewrite',
            url: 'urlrewrite/edit',
        },
    },
}
