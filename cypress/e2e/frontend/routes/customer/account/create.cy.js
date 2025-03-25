function generateRandomEmail() {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let email = '';
    for (let i = 0; i < 16; i++) {
        email += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return email + '@example.com';
}

describe('Checks admin cms routes', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/customer/account/create');
    });

    it('Checks the Create Account page title', () => {
        cy.get('h1').should('include.text', 'Create an Account');
    });

    it('Submits empty form', () => {
        cy.get('#form-validate button[type="submit"]').click();
        cy.get('#advice-required-entry-firstname').should('include.text', 'This is a required field.');
        cy.get('#advice-required-entry-lastname').should('include.text', 'This is a required field.');
        cy.get('#advice-required-entry-email_address').should('include.text', 'This is a required field.');
        cy.get('#advice-required-entry-password').should('include.text', 'This is a required field.');
        cy.get('#advice-required-entry-confirmation').should('include.text', 'This is a required field.');
    });

    it('Submits form with short password and wrong confirmation', () => {
        cy.get('#password').type('123');
        cy.get('#confirmation').type('abc');
        cy.get('#form-validate button[type="submit"]').click();
        cy.get('#advice-validate-password-password').should('include.text', 'Please enter more characters or clean leading or trailing spaces.');
        cy.get('#advice-validate-cpassword-confirmation').should('include.text', 'Please make sure your passwords match.');
    });

    it('Submits valid form', () => {
        const randomEmail = generateRandomEmail();
        cy.get('#firstname').type('John');
        cy.get('#lastname').type('Doe');
        cy.get('#email_address').type(randomEmail);
        cy.get('#password').type('12345678');
        cy.get('#confirmation').type('12345678');
        cy.get('#form-validate button[type="submit"]').click();
        cy.get('#advice-validate-password-password').should('include.text', 'Please enter more characters or clean leading or trailing spaces.');
        cy.get('#advice-validate-cpassword-confirmation').should('include.text', 'Please make sure your passwords match.');
    });
});