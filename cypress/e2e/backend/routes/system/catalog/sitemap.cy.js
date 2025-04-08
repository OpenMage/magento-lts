import { validation } from '../validation.js';

const route = {
    id: '#section-sitemap',
    url: 'system_config/edit/section/sitemap',
    h3: 'Google Sitemap',
    validate: {
        priority: {
            _input: {
                category: '#sitemap_category_priority',
                product: '#sitemap_product_priority',
                page: '#sitemap_page_priority',
            }
        }
    }
}

describe('Checks admin system sitemap settings', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string priority`, () => {
        Object.keys(route.validate.priority._input).forEach(field => {
            const selector = route.validate.priority._input[field];
            cy
                .get(selector)
                .clear({ force: true })
                .type(validation.assert.string, { force: true })
                .should('have.value', validation.assert.string);
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        cy.get('#advice-validate-number-sitemap_category_priority').should('include.text', validation.errors.number);
        cy.get('#advice-validate-number-sitemap_product_priority').should('include.text', validation.errors.number);
        cy.get('#advice-validate-number-sitemap_page_priority').should('include.text', validation.errors.number);
    });

    it(`tests invalid number priority`, () => {
        Object.keys(route.validate.priority._input).forEach(field => {
            const selector = route.validate.priority._input[field];
            cy
                .get(selector)
                .clear({ force: true })
                .type(validation.assert.numberGreater1, { force: true })
                .should('have.value', validation.assert.numberGreater1);
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        cy.get('#advice-validate-number-range-sitemap_category_priority').should('include.text', validation.errors.numberRange);
        cy.get('#advice-validate-number-range-sitemap_product_priority').should('include.text', validation.errors.numberRange);
        cy.get('#advice-validate-number-range-sitemap_page_priority').should('include.text', validation.errors.numberRange);
    });

    it(`tests empty priority`, () => {
        Object.keys(route.validate.priority._input).forEach(field => {
            const selector = route.validate.priority._input[field];
            cy
                .get(selector)
                .clear({ force: true })
                .should('have.value', '');
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        cy.get('#advice-required-entry-sitemap_category_priority').should('include.text', validation.errors.requiredEntry);
        cy.get('#advice-required-entry-sitemap_product_priority').should('include.text', validation.errors.requiredEntry);
        cy.get('#advice-required-entry-sitemap_page_priority').should('include.text', validation.errors.requiredEntry);
    });
});