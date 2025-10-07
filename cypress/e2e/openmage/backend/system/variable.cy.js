const test = cy.testBackendSystemVariable.config;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests save empty, no js`, () => {
        test.index.clickAdd();
        validation.removeClasses(test.new);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        test.new.clickSave();
        validation.hasErrorMessage('Validation has failed.', 'have.text');
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests new route`, () => {
        test.index.clickAdd();
        validation.pageElements(test, test.new);
    });

    it(`tests edit route`, () => {
        // TODO: There is no edit route for system variables
        validation.pageElements(test, test.index);
    });
});
