const test = cy.testBackendSystemDesign.config;
const tools = cy.openmage.tools;
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
        const error = 'Your design change for the specified store intersects with another one, please specify another date range.';
        test.new.clickSave();
        validation.hasErrorMessage(error,'have.text');
        cy.get('#messages').screenshot('saveEmptyWithoutJs.message.system.design', { overwrite: true, padding: 10 });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        // TODO: There is no edit route for design updates
        validation.pageElements(test, test.index);
    });

    it(`tests new route`, () => {
        test.index.clickAdd();
        validation.pageElements(test, test.new);
    });
});
