describe('Check newsletter subribe', () => {
    it('Test empty input', () => {
        cy.visit('/')
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('#advice-required-entry-newsletter').should('include.text', 'Please enter a valid email address.');
    })

    it('Test valid input', () => {
        cy.visit('/')
        cy.get('#newsletter').type('test@·example.com');
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('.success-msg').should('include.text', 'Thank you for your subscription.');
    })
})
