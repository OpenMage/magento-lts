const test = cy.openmage.test.frontend.catalog.category.config;

describe('Check catalog category page', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.url);
    });

    it('tests swatch: color', () => {
        const options = 'ul.configurable-swatch-color';
        const swatchLink = 'li a.swatch-link';
        const colors = ['Charcoal', 'Khaki', 'Red', 'Royal Blue'];
        const images = ['msj006t', 'msj006c-khaki', 'msj006c-red', 'msj006c-royal-blue'];

        cy.get(options).eq(0).find(swatchLink).should('have.length', 4);

        cy.get(options).eq(0).find(swatchLink).each((swatch, index) => {
            cy.wrap(swatch).invoke('attr', 'title').should('eq', colors[index]);
            cy.wrap(swatch).click();
            cy.get('img.product-collection-image-404').should('have.attr', 'src').should('include', images[index]);
            cy.wait(500);
        });
    });
})
