const test = cy.openmage.test.frontend.customer.account.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe('Checks customer account create', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.create);
    });

    it('Checks the Create Account page title', () => {
        cy.get(test._title).should('include.text', test.create.title);
    });

    it('tests save empty, no js', () => {
        validation.fillFields(test.create, validation.requiredEntry);
        validation.removeClasses(test.create);
        tools.click(test._buttonSubmit);
        cy.get(validation._errorMessage)
            .should('include.text', '"First Name" is a required value.')
            .should('include.text', '"First Name" length must be equal or greater than 1 characters.')
            .should('include.text', '"Last Name" is a required value.')
            .should('include.text', '"Last Name" length must be equal or greater than 1 characters.')
            .should('include.text', '"Email" is a required value.');
    });

    it('Submits form with short password and wrong confirmation', () => {
        cy.get(test.create.__fields.password.selector).type('123').should('have.value', '123');
        cy.get(test.create.__fields.confirmation.selector).type('abc').should('have.value', 'abc');
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
        cy.get(test.create.__fields.firstname.selector).type(firstname).should('have.value', firstname);
        cy.get(test.create.__fields.lastname.selector).type(lastname).should('have.value', lastname);
        cy.get(test.create.__fields.email_address.selector).type(email).should('have.value', email);
        cy.get(test.create.__fields.password.selector).type(password).should('have.value', password);
        cy.get(test.create.__fields.confirmation.selector).type(password).should('have.value', password);
        tools.click(test._buttonSubmit);
        validation.hasSuccessMessage(message, {screenshot: false, filename: 'message.customer.account.create.success'});
    });
});
