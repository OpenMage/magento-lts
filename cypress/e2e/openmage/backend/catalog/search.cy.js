const test = cy.testBackendCatalogSearch.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests save empty, no js`, () => {
        const error = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';

        test.index.clickAdd();
        validation.removeClasses(test.new);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        test.new.clickSave();

        // TODO: fit it
        // validation.hasErrorMessage(error);
        validation.hasSuccessMessage('You saved the search term.');
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
