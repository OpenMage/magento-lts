import { paths } from '../../../../../../support/paths.js';
import { validation } from '../../../../../../support/validation.js';

const route = paths.backend.system.config.customers.promo;
const validate = {
    _group: {
        couponCodes: {
            _id: '#promo_auto_generated_coupon_codes-head',
            _input: {
                length: '#promo_auto_generated_coupon_codes_length',
                dashes: '#promo_auto_generated_coupon_codes_dash',
            }
        }
    }
}

describe(`Checks admin system "${route.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string input`, () => {
        cy.get('body').then($body => {
            if (!$body.find(validate._group.couponCodes._id).hasClass('open')) {
                cy.get(validate._group.couponCodes._id).click({force: true});
            }
        });

        Object.keys(validate._group.couponCodes._input).forEach(field => {
            const selector = validate._group.couponCodes._input[field];
            const value = validation.assert.string;

            cy
                .get(selector)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);
        });

        cy.adminSaveConfiguration();

        cy.log('Checking for error messages');
        const error = validation.errors.digits;
        cy.get('#advice-validate-digits-promo_auto_generated_coupon_codes_length').should('include.text', error);
        cy.get('#advice-validate-digits-promo_auto_generated_coupon_codes_dash').should('include.text', error);
    });
});
