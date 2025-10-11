const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.queue;

/**
 * Configuration for "Newsletter Queue" menu item
 * @type {{_title: string, _id: string, _id_parent: string, url: string, index: {}}}
 */
test.config = {
    _id: '#nav-admin-newsletter-queue',
    _id_parent: '#nav-admin-newsletter',
    _title: base._title,
    url: 'newsletter_queue/index',
    index: {},
}

/**
 * Configuration for "Newsletter Queue" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Newsletter Queue',
    url: test.config.url,
    _grid: '#queueGrid_table',
}
