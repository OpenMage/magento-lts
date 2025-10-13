const test = cy.openmage.test.backend.customer.customer.config;
const tools = cy.openmage.tools;
const utils = cy.openmage.utils;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.clickAdd();
        validation.removeClasses(test.new);

        test.new.clickSaveAndContinue();
        validation.hasErrorMessage('"First Name" is a required value.');
        validation.hasErrorMessage('"First Name" length must be equal or greater than 1 characters.');
        validation.hasErrorMessage('"Last Name" is a required value.');
        validation.hasErrorMessage('"Last Name" length must be equal or greater than 1 characters.');
        validation.hasErrorMessage('"Email" is a required value.');
        utils.screenshot(cy.openmage.validation._messagesContainer, 'message.customer.customer.saveEmptyWithoutJs');
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        test.index.clickGridRow('John Doe');
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        test.index.clickAdd();
        validation.pageElements(test, test.new);
    });
});
