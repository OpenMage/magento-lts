describe('Check newsletter subribe', () => {
    it('Test empty input', () => {
        cy.visit('/')
        cy.get('#newsletter').should('have.value', '');
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('#advice-required-entry-newsletter').should('include.text', 'This is a required field.');
    })

    it('Test valid input twice', () => {
        const email = cy.openmage.tools.generateRandomEmail();
        cy.visit('/')
        cy.get('#newsletter').type(email).should('have.value', email);
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('.success-msg').should('include.text', 'Thank you for your subscription.');

        cy.get('#newsletter').type(email).should('have.value', email);
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('.error-msg').should('include.text', 'There was a problem with the subscription: This email address is already registered.');
    })
})
