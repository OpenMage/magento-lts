const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.config.catalog.sitemap;

/**
 * Configuration for admin system "Google Sitemap" settings
 * @type {{_: string, _nav: string, _title: string, url: string, section: {}}}
 */
test.config = {
    _: '#nav-admin-system-config',
    _nav: '#nav-admin-system',
    _title: base._title,
    url: 'system_config/index',
    section: {},
}

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
 * Fields for "Priority" group
 * @type {{__fields: {product: {_: string}, page: {_: string}, category: {_: string}}}}
 */
test.config.section.priority = {
    __fields: {
        category: {
            _: '#sitemap_category_priority',
        },
        product: {
            _: '#sitemap_product_priority',
        },
        page: {
            _: '#sitemap_page_priority',
        },
    }
};
