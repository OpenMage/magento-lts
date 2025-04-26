const route = cy.testRoutes.frontend.customer.account.create;
const validation = cy.openmage.validation;

describe('Checks customer account create', () => {
    beforeEach('Go to page', () => {
        cy.visit(route.url);
    });

    it('Checks the Create Account page title', () => {
        cy.get(route._h1).should('include.text', route.h1);
    });

    it('Submits empty form', () => {
        validation.fillFields(route.__validation._input, validation.requiredEntry);
        validation.saveAction(route._buttonSubmit);
        validation.validateFields(route.__validation._input, validation.requiredEntry);
    });

    it('Submits empty form, no js', () => {
        validation.fillFields(route.__validation._input, validation.requiredEntry);
        validation.removeClasses(route.__validation._input);
        validation.saveAction(route._buttonSubmit);
        cy.get(validation._errorMessage)
            .should('include.text', '"First Name" is a required value.')
            .should('include.text', '"First Name" length must be equal or greater than 1 characters.')
            .should('include.text', '"Last Name" is a required value.')
            .should('include.text', '"Last Name" length must be equal or greater than 1 characters.')
            .should('include.text', '"Email" is a required value.');
    });

    it('Submits form with short password and wrong confirmation', () => {
        cy.get(route.__validation._input.password).type('123').should('have.value', '123');
        cy.get(route.__validation._input.confirmation).type('abc').should('have.value', 'abc');
        cy.get(route._buttonSubmit).click();
        cy.get('#advice-validate-password-password').should('include.text', 'Please enter more characters or clean leading or trailing spaces.');
        cy.get('#advice-validate-cpassword-confirmation').should('include.text', 'Please make sure your passwords match.');
    });

    it('Submits valid form with random email', () => {
        const email = cy.openmage.tools.generateRandomEmail();
        const firstname = 'John';
        const lastname = 'Doe';
        const password = '12345678';
        const successMsg = 'Thank you for registering with Madison Island.';
        cy.get(route.__validation._input.firstname).type(firstname).should('have.value', firstname);
        cy.get(route.__validation._input.lastname).type(lastname).should('have.value', lastname);
        cy.get(route.__validation._input.email_address).type(email).should('have.value', email);
        cy.get(route.__validation._input.password).type(password).should('have.value', password);
        cy.get(route.__validation._input.confirmation).type(password).should('have.value', password);
        cy.get(route._buttonSubmit).click();
        cy.get(validation._successMessage).should('include.text', successMsg);
    });
});
