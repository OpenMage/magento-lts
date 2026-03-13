const test = cy.openmage.test.backend.customer.customer.config;
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
        validation.hasErrorMessage('"First Name" is a required value.');
        validation.hasErrorMessage('"First Name" length must be equal or greater than 1 characters.');
        validation.hasErrorMessage('"Last Name" is a required value.');
        validation.hasErrorMessage('"Last Name" length must be equal or greater than 1 characters.');
        validation.hasErrorMessage('"Email" is a required value.');
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'asc');
    });

    it(`tests edit route`, () => {
        test.index.clickGridRow('John Doe');
        validation.pageElements(test, test.edit);

        test.edit.__buttons.saveAndContinue.click(test.edit.url);
        validation.hasSuccessMessage('The customer has been saved.');

        test.edit.__buttons.reset.click(test.edit.url);
        test.edit.__buttons.back.click(test.index.url);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click(test.new.url);
        test.new.__buttons.back.click(test.index.url);
    });
});
