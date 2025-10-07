const test = cy.testBackendCustomerCustomer.config;
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

        test.new.clickSaveAndContinue();
        validation.hasErrorMessage('"First Name" is a required value.');
        validation.hasErrorMessage('"First Name" length must be equal or greater than 1 characters.');
        validation.hasErrorMessage('"Last Name" is a required value.');
        validation.hasErrorMessage('"Last Name" length must be equal or greater than 1 characters.');
        validation.hasErrorMessage('"Email" is a required value.');
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
