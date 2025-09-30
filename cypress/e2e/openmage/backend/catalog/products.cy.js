const route = cy.testRoutes.backend.catalog.products;

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(route);
    });

    it(`tests classes and title`, () => {
        cy.adminTestRoute(route);
    });

    it(`tests filter options`, () => {
        cy.log('Checking for the number of filter type options');
        cy.get('#productGrid_product_filter_type option').should('have.length', 7);

        cy.log('Checking for the number of filter visibility options');
        cy.get('#productGrid_product_filter_visibility option').should('have.length', 5);
    });
});