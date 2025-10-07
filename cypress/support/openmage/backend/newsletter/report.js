cy.testBackendNewsletterReport = {};

cy.testBackendNewsletterReport.config = {
    _id: '#nav-admin-newsletter-problem',
    _id_parent: '#nav-admin-newsletter',
    _h3: 'h3.icon-head',
}

cy.testBackendNewsletterReport.config.index = {
    title: 'Newsletter Problem Reports',
    url: 'newsletter_problem/index',
    _grid: '#problemGrid_table',
}
