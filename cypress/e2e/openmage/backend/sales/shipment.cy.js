const test = cy.testBackendSalesOrderShipment.config;
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
        tools.grid.clickFirstRow(test.index);
        validation.pageElements(test, test.view);
    });
});
