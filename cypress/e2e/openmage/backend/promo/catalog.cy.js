const test = cy.openmage.test.backend.promo.catalog.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.__buttons.add.click();
        validation.removeClassesAll();

        // TODO: fix it
        const message = 'The rule has been saved.';
        const screenshot = 'message.promo.catalog.saveEmptyWithoutJs';
        test.new.__buttons.saveAndContinue.click();
        validation.hasSuccessMessage(message, { match: 'have.text', screenshot: true, filename: screenshot });
        validation.hasErrorMessage();
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'desc');
    });

    it(`tests edit route`, () => {
        tools.grid.clickFirstRow(test.index);
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);
    });
});
