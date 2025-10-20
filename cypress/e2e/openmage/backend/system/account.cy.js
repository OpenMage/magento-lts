const test = cy.openmage.test.backend.system.account.config;
const tools = cy.openmage.tools;
const utils = cy.openmage.utils;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty, no js`, () => {
        validation.removeClasses(test.index);
        validation.emptyFields(test.index);

        const message = 'Current password field cannot be empty.';
        const screenshot = 'message.system.account.saveEmptyWithoutJs';
        test.index.__buttons.save.click();
        validation.hasErrorMessage(message, { screenshot: true, filename: screenshot + '-1' });

        /// with filling password
        validation.removeClasses(test.index);
        validation.emptyFields(test.index);
        cy.get(test.index.__fields.current_password._)
            .type(cy.openmage.admin.password.value)
            .should('have.value', cy.openmage.admin.password.value);
        test.index.__buttons.save.click();
        validation.hasErrorMessage('User Name is required field.');
        validation.hasErrorMessage('First Name is required field.');
        validation.hasErrorMessage('Last Name is required field.');
        validation.hasErrorMessage('Please enter a valid email.');
        utils.screenshot(cy.openmage.validation._messagesContainer, screenshot + '-2');
   });

    it(`tests save empty input`, () => {
        const validate = validation.requiredEntry;
        validation.fillFields(test.index, validate);
        test.index.__buttons.save.click();
        validation.validateFields(test.index, validate);
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });
});
