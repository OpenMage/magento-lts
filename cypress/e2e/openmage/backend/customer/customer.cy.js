const test = cy.openmage.test.backend.customer.customer.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
        cy.fixture(test.__fixture).as('fixture');
    });

    it(`tests save empty values, no js`, function () {
        test.index.__buttons.add.click();
        validation.fixture.removeClasses(this.fixture.default);

        tools.admin.buttons.clickSaveAndContinue(test.edit.url);
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

        tools.admin.buttons.clickSaveAndContinue(test.edit.url);
        validation.hasSuccessMessage('The customer has been saved.');

        tools.admin.buttons.clickReset(test.edit.url);
        tools.admin.buttons.clickBack(test.index.url);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        tools.admin.buttons.clickReset(test.new.url);
        tools.admin.buttons.clickBack(test.index.url);
    });
});
