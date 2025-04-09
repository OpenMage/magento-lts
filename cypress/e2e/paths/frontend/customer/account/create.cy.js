describe('Checks customer account create', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/customer/account/create');
    });

    it('Checks the Create Account page title', () => {
        cy.get('h1').should('include.text', 'Create an Account');
    });

    it('Submits empty form', () => {
        const error = 'This is a required field.';
        cy.get('#form-validate button[type="submit"]').click();
        cy.get('#advice-required-entry-firstname').should('include.text', error);
        cy.get('#advice-required-entry-lastname').should('include.text', error);
        cy.get('#advice-required-entry-email_address').should('include.text', error);
        cy.get('#advice-required-entry-password').should('include.text', error);
        cy.get('#advice-required-entry-confirmation').should('include.text', error);
    });

    it('Submits form with short password and wrong confirmation', () => {
        cy.get('#password').type('123').should('have.value', '123');
        cy.get('#confirmation').type('abc').should('have.value', 'abc');
        cy.get('#form-validate button[type="submit"]').click();
        cy.get('#advice-validate-password-password').should('include.text', 'Please enter more characters or clean leading or trailing spaces.');
        cy.get('#advice-validate-cpassword-confirmation').should('include.text', 'Please make sure your passwords match.');
    });

    it('Submits valid form with random email', () => {
        const email = cy.openmage.generateRandomEmail();
        cy.get('#firstname').type('John').should('have.value', 'John');
        cy.get('#lastname').type('Doe').should('have.value', 'Doe');
        cy.get('#email_address').type(email).should('have.value', email);
        cy.get('#password').type('12345678').should('have.value', '12345678');
        cy.get('#confirmation').type('12345678').should('have.value', '12345678');
        cy.get('#form-validate button[type="submit"]').click();
        cy.get('.success-msg').should('include.text', 'Thank you for registering with Madison Island.');
    });
});
