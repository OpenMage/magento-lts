describe('Check canonical tag', () => {
    const canonical = 'head link[rel="canonical"]';

    it('tests page has canonical tag', () => {
        cy.visit('/');
        cy.get(canonical).should('exist').should('have.attr', 'href');

        cy.visit('/men.html');
        cy.get(canonical).should('exist').should('have.attr', 'href');
    });

    it('tests page has no canonical tag', () => {
        cy.visit('/no-route');
        cy.get(canonical).should('not.exist');
    });
})
