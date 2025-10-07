cy.testBackendNewsletterSubscriber = {};

cy.testBackendNewsletterSubscriber.config = {
    _id: '#nav-admin-newsletter-subscriber',
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletterSubscriber.config.index = {
    title: 'Newsletter Subscribers',
    url: 'newsletter_subscriber/index',
    _grid: '#subscriberGrid_table',
}
