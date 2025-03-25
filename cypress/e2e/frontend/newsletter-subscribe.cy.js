function generateRandomEmail() {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let email = '';
    for (let i = 0; i < 16; i++) {
        email += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return email + '@example.com';
}

describe('Check newsletter subribe', () => {
    it('Test empty input', () => {
        cy.visit('/')
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('#advice-required-entry-newsletter').should('include.text', 'This is a required field.');
    })

    it('Test valid input twice', () => {
        const randomEmail = generateRandomEmail();
        cy.visit('/')
        cy.get('#newsletter').type(randomEmail);
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('.success-msg').should('include.text', 'Thank you for your subscription.');

        cy.get('#newsletter').type(randomEmail);
        cy.get('#newsletter-validate-detail button[type="submit"]').click();
        cy.get('.success-msg').should('include.text', 'There was a problem with the subscription: This email address is already registered.');
    })
})
