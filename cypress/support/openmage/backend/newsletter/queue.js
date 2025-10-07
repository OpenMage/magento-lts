cy.testBackendNewsletterQueue = {};

cy.testBackendNewsletterQueue.config = {
    _id: '#nav-admin-newsletter-queue',
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletterQueue.config.index = {
    title: 'Newsletter Queue',
    url: 'newsletter_queue/index',
    _grid: '#queueGrid_table',
}
