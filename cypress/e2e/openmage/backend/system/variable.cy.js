const test = cy.openmage.test.backend.system.variable.config;
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

        test.new.__buttons.saveAndContinue.click();
        validation.hasErrorMessage('Validation has failed.', { match: 'have.text' });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'desc');
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click(test.new.url);
        test.new.__buttons.back.click(test.index.url);
    });

    it(`tests edit route`, () => {
        // TODO: There is no sample data for custom variables, need to create one first
        validation.pageElements(test, test.index);

        //test.edit.__buttons.reset.click(test.edit.url);
        //test.edit.__buttons.back.click(test.index.url);
    });
});
