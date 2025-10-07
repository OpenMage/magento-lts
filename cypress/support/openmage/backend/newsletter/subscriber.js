const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.subscriber;

/**
 * Configuration for "Newsletter Subscribers" menu item
 * @type {{_title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-newsletter-subscriber',
    _id_parent: '#nav-admin-newsletter',
    _title: base._title,
    url: 'newsletter_subscriber/index',
}

/**
 * Configuration for "Newsletter Subscribers" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Newsletter Subscribers',
    url: test.config.url,
    _grid: '#subscriberGrid_table',
}
