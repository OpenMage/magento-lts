const test = cy.openmage.test.backend.system.config.catalog.sitemap;

/**
 * Section "Google Sitemap"
 * @type {{_: string, title: string, url: string}}
 */
test.config.section = {
    _: '#section-sitemap',
    title: 'Google Sitemap',
    url: 'system_config/edit/section/sitemap',
}

/**
 * Category settings
 * @type {{__fields: {priority: {_: string}}}}
 */
test.config.section.category = {
    __fields: {
        priority: {
            _: '#sitemap_category_priority',
        },
    }
};

/**
 * Product settings
 * @type {{__fields: {priority: {_: string}}}}
 */
test.config.section.product = {
    __fields: {
        priority: {
            _: '#sitemap_product_priority',
        },
    }
};

/**
 * Page settings
 * @type {{__fields: {priority: {_: string}}}}
 */
test.config.section.page = {
    __fields: {
        priority: {
            _: '#sitemap_page_priority',
        },
    }
};

/**
 * Group of priority fields
 * @type {{__fields: {product: {_: string}, page: {_: string}, category: {_: string}}}}
 * @private
 */
test.config.section.__groupPriority = {
    __fields: {
        category: test.config.section.category.__fields.priority,
        page: test.config.section.page.__fields.priority,
        product: test.config.section.product.__fields.priority,
    }
}