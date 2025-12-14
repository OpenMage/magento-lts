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
        test.index.__buttons.add.click();
        validation.removeClasses(test.new);

        test.new.__buttons.saveAndContinue.click();
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

        test.edit.__buttons.saveAndContinue.click();
        const message = 'The customer has been saved.';
        validation.hasSuccessMessage(message);
        cy.url().should('include', test.edit.url);

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
