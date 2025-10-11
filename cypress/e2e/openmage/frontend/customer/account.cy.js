const test = cy.openmage.test.frontend.customer.account.config;
const tools = cy.openmage.tools;
const utils = cy.openmage.utils;
const validation = cy.openmage.validation;

describe('Checks customer account create', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.create.url);
    });

    it('Checks the Create Account page title', () => {
        cy.get(test._title).should('include.text', test.create.title);
    });

    it('tests save empty, no js', () => {
        validation.fillFields(test.create, validation.requiredEntry);

        validation.removeClasses(test.create);
        tools.click(test._buttonSubmit);

        cy.get(validation._errorMessage);
        validation.hasErrorMessage('"First Name" is a required value.')
        validation.hasErrorMessage('"First Name" length must be equal or greater than 1 characters.')
        validation.hasErrorMessage('"Last Name" is a required value.')
        validation.hasErrorMessage('"Last Name" length must be equal or greater than 1 characters.')
        validation.hasErrorMessage('"Email" is a required value.');
        validation.hasErrorMessage('"Email" is a required value.');
        utils.screenshot(validation._errorMessage, 'message.frontend.customer.account.saveEmptyWithoutJs');
    });

    it('Submits form with short password and wrong confirmation', () => {
        cy.get(test.create.__fields.password._).type('123').should('have.value', '123');
        cy.get(test.create.__fields.confirmation._).type('abc').should('have.value', 'abc');
        tools.click(test._buttonSubmit);
        cy.get('#advice-validate-password-password').should('include.text', 'Please enter more characters or clean leading or trailing spaces.');
        cy.get('#advice-validate-cpassword-confirmation').should('include.text', 'Please make sure your passwords match.');
    });

    it('Submits empty form', () => {
        validation.fillFields(test.create, validation.requiredEntry);
        tools.click(test._buttonSubmit);
        validation.validateFields(test.create, validation.requiredEntry);
    });

    it('Submits valid form with random email', () => {
        const email = cy.openmage.utils.generateRandomEmail();
        const firstname = 'John';
        const lastname = 'Doe';
        const password = '12345678';

        const message = 'Thank you for registering with Madison Island.';
        const filename = 'message.customer.account.create.success';
        cy.get(test.create.__fields.firstname._).type(firstname).should('have.value', firstname);
        cy.get(test.create.__fields.lastname._).type(lastname).should('have.value', lastname);
        cy.get(test.create.__fields.email_address._).type(email).should('have.value', email);
        cy.get(test.create.__fields.password._).type(password).should('have.value', password);
        cy.get(test.create.__fields.confirmation._).type(password).should('have.value', password);
        tools.click(test._buttonSubmit);
        validation.hasSuccessMessage(message, { screenshot: false, filename: filename });
    });
});
