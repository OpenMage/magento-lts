const test = cy.openmage.test.backend.newsletter.template.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.clickAdd();
        validation.removeClassesFromInput();
        validation.removeClassesFromTextarea();

        // TODO: add save and continue functionality
        const message = 'You must give a non-empty value for field \'template_code\'';
        const filename = 'message.newsletter.template.saveEmptyWithoutJs';
        test.new.clickSave();
        validation.hasErrorMessage(message, { match: 'have.text', screenshot: true, filename: filename });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        tools.grid.clickFirstRow(test.index);
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        test.index.clickAdd();
        validation.pageElements(test, test.new);
    });
});
