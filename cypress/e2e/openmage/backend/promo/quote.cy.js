const test = cy.testBackendPromoQuote.config;
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
        tools.clickContains(test.index._grid, 'td', '$500');
        check.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        tools.click(test.index.__buttons.add);
        check.pageElements(test, test.new);
    });
});
