const test = cy.openmage.test.frontend.catalog.product.config;

describe('Check catalog product page', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.url);
    });

    it('tests swatch: color', () => {
        const options = 'ul#configurable_swatch_color li a.swatch-link';
        cy.get(options).should('have.length', 4);
        cy.get(options).eq(0).invoke('attr', 'title').should('eq', 'Charcoal');
        cy.get(options).eq(1).invoke('attr', 'title').should('eq', 'Khaki');
        cy.get(options).eq(2).invoke('attr', 'title').should('eq', 'Red');
        cy.get(options).eq(3).invoke('attr', 'title').should('eq', 'Royal Blue');
    });

    it('tests swatch: size', () => {
        const options = 'ul#configurable_swatch_size li a.swatch-link';
        cy.get(options).should('have.length', 5);
        cy.get(options).eq(0).contains('XS').should('exist');
        cy.get(options).eq(1).contains('S').should('exist');
        cy.get(options).eq(2).contains('M').should('exist');
        cy.get(options).eq(3).contains('L').should('exist');
        cy.get(options).eq(4).contains('XL').should('exist');
        cy.get(options).eq(0).invoke('attr', 'title').should('eq', 'XS')
        cy.get(options).eq(1).invoke('attr', 'title').should('eq', 'S')
        cy.get(options).eq(2).invoke('attr', 'title').should('eq', 'M')
        cy.get(options).eq(3).invoke('attr', 'title').should('eq', 'L')
        cy.get(options).eq(4).invoke('attr', 'title').should('eq', 'XL')
    });
})
