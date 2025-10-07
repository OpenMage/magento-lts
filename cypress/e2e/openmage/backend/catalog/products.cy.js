const test = cy.testBackendCatalogProducts.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        tools.clickContains(test.index._grid, 'td', '905');
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        tools.click(test.index.__buttons.add);
        validation.pageElements(test, test.new);
    });

    it(`tests filter options`, () => {
        cy.log('Checking for the number of filter type options');
        cy.get('#productGrid_product_filter_type option').should('have.length', 7);

        cy.log('Checking for the number of filter visibility options');
        cy.get('#productGrid_product_filter_visibility option').should('have.length', 5);
    });
});
