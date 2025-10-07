const test = cy.testBackendSystemDesign.config;
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
        // TODO: There is no edit route for design updates
        validation.pageElements(test, test.index);
    });

    it(`tests new route`, () => {
        tools.click(test.index.__buttons.add);
        validation.pageElements(test, test.new);
    });
});
