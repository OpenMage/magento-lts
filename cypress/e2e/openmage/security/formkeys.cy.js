describe('Check form_key exists', () => {
    it('has form_key in contacts form', () => {
        cy.visit('/contacts')
        cy.get('#contactForm input[name="form_key"]').should('exist')
    })

    it('has form_key in newsletter subscribe form', () => {
        cy.visit('/')
        cy.get('#newsletter-validate-detail input[name="form_key"]').should('exist')
    })
})
