const test = cy.testBackendSalesInvoice.config;
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

    it(`tests view route`, () => {
        tools.clickGridRow(test.index._grid, 'td', '100000040');
        check.pageElements(test, test.view);
    });
});
