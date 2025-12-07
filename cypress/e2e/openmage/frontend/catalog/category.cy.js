const test = cy.openmage.test.frontend.catalog.category.config;

describe('Check catalog category page', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.url);
    });

    it('tests swatch: color', () => {
        const options = 'ul.configurable-swatch-color';
        const swatch = 'li a.swatch-link';

        cy.get(options).eq(0).find(swatch).should('have.length', 4);
        cy.get(options).eq(0).find(swatch).eq(0).invoke('attr', 'title').should('eq', 'Red');
        cy.get(options).eq(0).find(swatch).eq(1).invoke('attr', 'title').should('eq', 'Khaki');
        cy.get(options).eq(0).find(swatch).eq(2).invoke('attr', 'title').should('eq', 'Charcoal');
        cy.get(options).eq(0).find(swatch).eq(3).invoke('attr', 'title').should('eq', 'Royal Blue');
    });
})
