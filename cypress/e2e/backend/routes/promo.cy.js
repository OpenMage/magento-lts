const navMenuItem = {
    promo: '#nav-admin-promo',
}

const routes = {
    //promo
    CatalogRules: {
        parent: navMenuItem.promo,
        id: '#nav-admin-promo-catalog',
        url: '/admin/promo_catalog/index',
        h3: 'Catalog Price Rules',
    },
    CartRules: {
        parent: navMenuItem.promo,
        id: '#nav-admin-promo-quote',
        url: '/admin/promo_quote/index',
        h3: 'Shopping Cart Price Rules',
    }
};

describe('Checks admin promo routes', () => {
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