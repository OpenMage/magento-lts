const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.newsletter.subscriber;

/**
 * Configuration for "Newsletter Subscribers" menu item
 * @type {{_: string, _nav: string, _title: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-newsletter-subscriber',
    _nav: '#nav-admin-newsletter',
    _title: base._title,
    url: 'newsletter_subscriber/index',
    index: {},
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
