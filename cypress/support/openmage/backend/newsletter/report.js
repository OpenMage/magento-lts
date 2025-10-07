const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.report;

/**
 * Configuration for "Newsletter Problem Reports" menu item
 * @type {{_title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-newsletter-problem',
    _id_parent: '#nav-admin-newsletter',
    _title: base._title,
    url: 'newsletter_problem/index',
}

/**
 * Configuration for "Newsletter Problem Reports" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Newsletter Problem Reports',
    url: test.config.url,
    _grid: '#problemGrid_table',
}
