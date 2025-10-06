const test = cy.testFrontend.customer.account.create;
const fields = test.__validation._input;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe('Checks customer account create', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.url);
    });

    it('Checks the Create Account page title', () => {
        cy.get(test._h1).should('include.text', test.h1);
    });

    it('Submits empty form', () => {
        validation.fillFields(fields, validation.requiredEntry);
        tools.click(test._buttonSubmit);
        validation.validateFields(fields, validation.requiredEntry);
    });

    it('Submits empty form, no js', () => {
        validation.fillFields(fields, validation.requiredEntry);
        validation.removeClasses(fields);
        tools.click(test._buttonSubmit);
        cy.get(validation._errorMessage)
            .should('include.text', '"First Name" is a required value.')
            .should('include.text', '"First Name" length must be equal or greater than 1 characters.')
            .should('include.text', '"Last Name" is a required value.')
            .should('include.text', '"Last Name" length must be equal or greater than 1 characters.')
            .should('include.text', '"Email" is a required value.');
    });

    it('Submits form with short password and wrong confirmation', () => {
        cy.get(fields.password).type('123').should('have.value', '123');
        cy.get(fields.confirmation).type('abc').should('have.value', 'abc');
        tools.click(test._buttonSubmit);
        cy.get('#advice-validate-password-password').should('include.text', 'Please enter more characters or clean leading or trailing spaces.');
        cy.get('#advice-validate-cpassword-confirmation').should('include.text', 'Please make sure your passwords match.');
    });

    it('Submits valid form with random email', () => {
        const email = cy.openmage.utils.generateRandomEmail();
        const firstname = 'John';
        const lastname = 'Doe';
        const password = '12345678';
        const successMsg = 'Thank you for registering with Madison Island.';
        cy.get(fields.firstname).type(firstname).should('have.value', firstname);
        cy.get(fields.lastname).type(lastname).should('have.value', lastname);
        cy.get(fields.email_address).type(email).should('have.value', email);
        cy.get(fields.password).type(password).should('have.value', password);
        cy.get(fields.confirmation).type(password).should('have.value', password);
        tools.click(test._buttonSubmit);
        validation.hasSuccessMessage(successMsg);
    });
});
