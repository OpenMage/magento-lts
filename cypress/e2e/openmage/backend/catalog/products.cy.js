const test = cy.testBackendCatalogProducts.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests index route`, () => {
        check.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        tools.clickGridRow(test.index._grid, 'td', '905');
        check.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        tools.clickAction(test.index.__buttons.add);
        check.pageElements(test, test.new);
    });

    it(`tests filter options`, () => {
        cy.log('Checking for the number of filter type options');
        cy.get('#productGrid_product_filter_type option').should('have.length', 7);

        cy.log('Checking for the number of filter visibility options');
        cy.get('#productGrid_product_filter_visibility option').should('have.length', 5);
    });
});
