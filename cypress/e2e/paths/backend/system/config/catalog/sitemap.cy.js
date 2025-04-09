import { paths } from '../../../../../../support/paths.js';
import { validation } from '../../../../../../support/validation.js';

const route = paths.backend.system.config.catalog.sitemap;
const validate = {
    priority: {
        _input: {
            category: '#sitemap_category_priority',
            product: '#sitemap_product_priority',
            page: '#sitemap_page_priority',
        }
    }
}

describe(`Checks admin system "${route.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string priority`, () => {
        Object.keys(validate.priority._input).forEach(field => {
            const selector = validate.priority._input[field];
            const value = validation.assert.string;

            cy
                .get(selector)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        const error = validation.errors.number;
        cy.get('#advice-validate-number-sitemap_category_priority').should('include.text', error);
        cy.get('#advice-validate-number-sitemap_product_priority').should('include.text', error);
        cy.get('#advice-validate-number-sitemap_page_priority').should('include.text', error);
    });

    it(`tests invalid number priority`, () => {
        Object.keys(validate.priority._input).forEach(field => {
            const selector = validate.priority._input[field];
            const value = validation.assert.numberGreater1;

            cy
                .get(selector)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        const error = validation.errors.numberRange;
        cy.get('#advice-validate-number-range-sitemap_category_priority').should('include.text', error);
        cy.get('#advice-validate-number-range-sitemap_product_priority').should('include.text', error);
        cy.get('#advice-validate-number-range-sitemap_page_priority').should('include.text', error);
    });

    it(`tests empty priority`, () => {
        Object.keys(validate.priority._input).forEach(field => {
            const selector = validate.priority._input[field];

            cy
                .get(selector)
                .clear({ force: true })
                .should('have.value', '');
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        const error = validation.errors.requiredEntry;
        cy.get('#advice-required-entry-sitemap_category_priority').should('include.text', error);
        cy.get('#advice-required-entry-sitemap_product_priority').should('include.text', error);
        cy.get('#advice-required-entry-sitemap_page_priority').should('include.text', error);
    });
});