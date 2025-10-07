const test = cy.testBackendSalesInvoice.config;
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

    it(`tests view route`, () => {
        tools.clickContains(test.index._grid, 'td', '100000040');
        validation.pageElements(test, test.view);
    });
});
