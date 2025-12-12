const test = cy.openmage.test.backend.system.config.catalog.sitemap;

/**
 * Section "Google Sitemap"
 * @type {{_: string, title: string, url: string}}
 */
test.section = {
    _: '#section-sitemap',
    title: 'Google Sitemap',
    url: 'system_config/edit/section/sitemap',
    category: {},
    page: {},
    product: {},
    __groupPriority: {},
}

/**
 * Category settings
 * @type {{__fields: {priority: {_: string}}}}
 */
test.section.category = {
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
test.section.product = {
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
test.section.page = {
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
test.section.__groupPriority = {
    __fields: {
        category: {
            _: test.section.category.__fields.priority._
        },
        page: {
            _: test.section.page.__fields.priority._
        },
        product: {
            _: test.section.product.__fields.priority._
        },
    }
}