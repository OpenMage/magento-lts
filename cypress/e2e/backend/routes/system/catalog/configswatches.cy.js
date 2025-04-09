import { validation } from '../validation.js';

const route = {
    id: '#section-configswatches',
    url: 'system_config/edit/section/configswatches',
    h3: 'Configurable Swatches',
    validate: {
        dimension: {
            _input: {
                productDetail: {
                    height: '#configswatches_product_detail_dimensions_height',
                    width: '#configswatches_product_detail_dimensions_width',
                },
                productList: {
                    height: '#configswatches_product_listing_dimensions_height',
                    width: '#configswatches_product_listing_dimensions_width',
                },
                layeredNav: {
                    height: '#configswatches_layered_nav_dimensions_height',
                    width: '#configswatches_layered_nav_dimensions_width',
                },
            }
        }
    }
}

describe('Checks admin system configswatches settings', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminGetConfiguration(route);
    });

    it(`tests non-digit dimensions`, () => {
        Object.keys(route.validate.dimension._input).forEach(section => {
            const group = route.validate.dimension._input[section];
            const value = validation.assert.float;
            const error = validation.errors.digits;

            cy
                .get(group.height)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);

            cy
                .get(group.width)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        cy.get('#advice-validate-digits-configswatches_product_detail_dimensions_height').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_product_detail_dimensions_width').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_product_listing_dimensions_height').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_product_listing_dimensions_width').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_layered_nav_dimensions_height').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_layered_nav_dimensions_width').should('include.text', error);
    });

    it(`tests empty dimensions`, () => {
        Object.keys(route.validate.dimension._input).forEach(field => {
            const selector = route.validate.dimension._input[field];
            const error = validation.errors.requiredEntry;

            cy
                .get(selector.height)
                .clear({ force: true })
                .should('have.value', '');

            cy
                .get(selector.width)
                .clear({ force: true })
                .should('have.value', '');
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        cy.get('#advice-required-entry-configswatches_product_detail_dimensions_height').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_product_detail_dimensions_width').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_product_listing_dimensions_height').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_product_listing_dimensions_width').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_layered_nav_dimensions_height').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_layered_nav_dimensions_width').should('include.text', error);
    });
});