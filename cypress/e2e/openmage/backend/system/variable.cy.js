const test = cy.openmage.test.backend.system.variable.config;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.clickAdd();
        validation.removeClasses(test.new);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        const message = 'Validation has failed.';
        test.new.clickSave();
        validation.hasErrorMessage(message, { match: 'have.text', screenshot: true, filename: 'message.system.variable.saveEmptyWithoutJs' });
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
