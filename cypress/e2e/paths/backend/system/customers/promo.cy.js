import { validation } from '../../../../../support/validation.js';

const route = {
    id: '#section-promo',
    url: 'system_config/edit/section/promo',
    h3: 'Promotions',
    validate: {
        _group: {
            couponCodes: {
                head: '#promo_auto_generated_coupon_codes-head',
                _input: {
                    length: '#promo_auto_generated_coupon_codes_length',
                    dashes: '#promo_auto_generated_coupon_codes_dash',
                }
            }
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
        cy.get('body').then($body => {
            if (!$body.find(route.validate._group.couponCodes.head).hasClass('open')) {
                cy.get(route.validate._group.couponCodes.head).click({force: true});
            }
        });

        Object.keys(route.validate._group.couponCodes._input).forEach(field => {
            const selector = route.validate._group.couponCodes._input[field];
            const value = validation.assert.string;

            cy
                .get(selector)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);
        });

        cy.adminSaveConfiguration();

        console.log('Checking for error messages');
        const error = validation.errors.digits;
        cy.get('#advice-validate-digits-promo_auto_generated_coupon_codes_length').should('include.text', error);
        cy.get('#advice-validate-digits-promo_auto_generated_coupon_codes_dash').should('include.text', error);
    });
});
