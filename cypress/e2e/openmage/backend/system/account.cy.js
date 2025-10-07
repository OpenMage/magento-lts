const test = cy.openmage.test.backend.system.account.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests empty input`, () => {
        const validate = validation.requiredEntry;
        validation.fillFields(test.index, validate);
        tools.click(test.index.__buttons.save);
        validation.validateFields(test.index, validate);
    });
});
