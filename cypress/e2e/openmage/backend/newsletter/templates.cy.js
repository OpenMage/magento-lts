const test = cy.testBackendNewsletterTemplates.config;
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
        tools.clickContains(test.index._grid, 'td', 'Example');
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        tools.click(test.index.__buttons.add);
        validation.pageElements(test, test.new);
    });
});
