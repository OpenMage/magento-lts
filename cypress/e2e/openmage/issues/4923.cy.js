describe('Check canocincal tag', () => {
    const canonical = 'head link[rel="canonical"]';

    it('tests page has canocincal tag', () => {
        cy.visit('/');
        cy.get(canonical).should('exist').should('have.attr', 'href');

        cy.visit('/men.html');
        cy.get(canonical).should('exist').should('have.attr', 'href');
    });

    it('tests page has no canocincal tag', () => {
        cy.visit('/no-route');
        cy.get(canonical).should('not.exist');
    });
})
