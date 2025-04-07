const navMenuItem = {
    newsletter: '#nav-admin-newsletter',
}

const routes = {
    Templates: {
        parent: navMenuItem.newsletter,
        id: '#nav-admin-newsletter-template',
        url: '/admin/newsletter_template/index',
        h3: 'Newsletter Templates',
    },
    Queue: {
        parent: navMenuItem.newsletter,
        id: '#nav-admin-newsletter-queue',
        url: '/admin/newsletter_queue/index',
        h3: 'Newsletter Queue',
    },
    Subscriber: {
        parent: navMenuItem.newsletter,
        id: '#nav-admin-newsletter-subscriber',
        url: '/admin/newsletter_subscriber/index',
        h3: 'Newsletter Subscribers',
    },
    Report: {
        parent: navMenuItem.newsletter,
        id: '#nav-admin-newsletter-problem',
        url: '/admin/newsletter_problem/index',
        h3: 'Newsletter Problem Reports',
    }
};

describe('Checks admin newsletter routes', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
    });

    Object.keys(routes).forEach(routeKey => {
        const route = routes[routeKey];
        it(`tests ${routeKey}`, () => {
            cy.adminTestRoute(route);
        });
    });
});