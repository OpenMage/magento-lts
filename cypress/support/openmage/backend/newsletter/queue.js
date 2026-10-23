const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.queue;

/**
 * Configuration for "Newsletter Queue" menu item
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-newsletter-queue',
    _nav: '#nav-admin-newsletter',
    _title: base._title,
    url: 'admin/newsletter_queue',
    index: {},
}

/**
 * Configuration for "Newsletter Queue" page
 * @type {{title: string, url: string, grid: {}}}
 */
test.config.index = {
    title: 'Newsletter Queue',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'start_at', dir: 'desc' } }},
}
