import { validation } from '../validation.js';

const route = {
    id: '#section-promo',
    url: 'system_config/edit/section/promo',
    h3: 'Promotions',
    validate: {
        _input: {
            length: '#promo_auto_generated_coupon_codes_length',
            dashes: '#promo_auto_generated_coupon_codes_dash',
        }
    }
}

describe('Checks admin system promo settings', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string input`, () => {
        Object.keys(route.validate._input).forEach(field => {
            const selector = route.validate._input[field];
            const value = validation.assert.string;
            const error = validation.errors.number;

            cy
                .get(selector)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        cy.get('#advice-validate-number-promo_auto_generated_coupon_codes_length').should('include.text', error);
        cy.get('#advice-validate-number-promo_auto_generated_coupon_codes_dash').should('include.text', error);
    });

    it(`tests empty value`, () => {
        Object.keys(route.validate._input).forEach(field => {
            const selector = route.validate._input[field];
            const error = validation.errors.requiredEntry;

            cy
                .get(selector)
                .clear({ force: true })
                .should('have.value', '');
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        cy.get('#advice-required-entry-promo_auto_generated_coupon_codes_length').should('include.text', error);
    });
});