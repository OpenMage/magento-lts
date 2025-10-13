const test = cy.openmage.test.backend.promo.quote.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.clickAdd();
        validation.removeClassesAll();

        // TODO: fix it
        const message = 'The rule has been saved.';
        const screenshotFilename = 'message.promo.quote.saveEmptyWithoutJs';
        test.new.clickSaveAndContinue();
        validation.hasSuccessMessage(message, { match: 'have.text', screenshot: true, filename: screenshotFilename });
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
