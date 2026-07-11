const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.report;

/**
 * Configuration for "Newsletter Problem Reports" menu item
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-newsletter-problem',
    _nav: '#nav-admin-newsletter',
    _title: base._title,
    url: 'admin/newsletter_problem',
    index: {},
}

/**
 * Configuration for "Newsletter Problem Reports" page
 * @type {{title: string, url: string, grid: {}}}
 */
test.config.index = {
    title: 'Newsletter Problem Reports',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'problem_id', dir: 'desc' } }},
}
