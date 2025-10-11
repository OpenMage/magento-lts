const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.config.catalog.sitemap;

/**
 * Configuration for admin system "Google Sitemap" settings
 * @type {{_id: string, _id_parent: string, _title: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-config',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    url: 'system_config/index',
}

/**
 * Section "Google Sitemap"
 * @type {{_id: string, title: string, url: string}}
 */
test.config.section = {
    _id: '#section-sitemap',
    title: 'Google Sitemap',
    url: 'system_config/edit/section/sitemap',
}

/**
 * Fields for "Priority" group
 * @type {{__fields: {product: {selector: string}, page: {selector: string}, category: {selector: string}}}}
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
