const test = cy.testBackendCatalogSitemap.config;
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
        const error = 'Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.';
        test.new.clickSave();
        validation.hasErrorMessage(error);
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        // TODO: There is no edit route for sitemaps
        validation.pageElements(test, test.index);
    });

    it(`tests new route`, () => {
        test.index.clickAdd();
        validation.pageElements(test, test.new);
    });
});
