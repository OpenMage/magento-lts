const test = cy.openmage.test.backend.catalog.search.config;
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
        validation.removeClasses(test.new);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        test.new.__buttons.save.click();

        // TODO: fit it
        const message = 'You saved the search term.';
        const screenshot = 'message.catalog.search.saveEmptyWithoutJs';
        // validation.hasErrorMessage(error);
        validation.hasSuccessMessage(message, { screenshot: true, filename: screenshot });
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

        test.edit.__buttons.reset.click();
        cy.url().should('include', test.edit.url);

        test.edit.__buttons.back.click();
        cy.url().should('include', test.index.url);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click();
        cy.url().should('include', test.new.url);

        test.new.__buttons.back.click();
        cy.url().should('include', test.index.url);
    });
});
