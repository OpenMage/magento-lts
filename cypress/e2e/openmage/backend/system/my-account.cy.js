const test = cy.testBackendSystemMyAccount.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests index route`, () => {
        check.pageElements(test, test.index);
    });

    it(`tests empty input`, () => {
        const validate = validation.requiredEntry;
        validation.fillFields(test.index.__validation._input, validate);
        tools.clickAction(test.index.__buttons.save);
        validation.validateFields(test.index.__validation._input, validate);
    });
});
