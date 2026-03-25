const test = cy.openmage.test.frontend.catalog.product.config;

describe('Check catalog product page', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.url);
    });

    it('tests swatch: color', () => {
        const options = 'ul#configurable_swatch_color';
        const swatchLink = 'li a.swatch-link';
        const colors = ['Charcoal', 'Khaki', 'Red', 'Royal Blue'];
        const images = ['msj006t_4', 'msj006c-khaki', 'msj006c-red', 'msj006c-royal-blue'];

        cy.get(options).eq(0).find(swatchLink).should('have.length', 4);

        cy.get(options).eq(0).find(swatchLink).each((swatch, index) => {
            cy.wrap(swatch).invoke('attr', 'title').should('eq', colors[index]);
            cy.wrap(swatch).click();
            cy.get('img.gallery-image.visible').should('have.attr', 'src').should('include', images[index]);
            cy.wait(500);
        });
    });

    it('tests swatch: size', () => {
        const options = 'ul#configurable_swatch_size';
        const swatchLink = 'li a.swatch-link';
        const sizes = ['XS', 'S', 'M', 'L', 'XL'];

        cy.get(options).eq(0).find(swatchLink).should('have.length', 5);

        cy.get(options).eq(0).find(swatchLink).each((swatch, index) => {
            cy.get(options).eq(0).contains(sizes[index]).should('exist');
            cy.wrap(swatch).invoke('attr', 'title').should('eq', sizes[index]);
        });
    });
})
