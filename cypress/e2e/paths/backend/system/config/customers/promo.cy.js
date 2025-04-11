const route = cy.testRoutes.backend.system.config.customers.promo;
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
                .should('have.value', value)
                .should('have.class', validation.digits.css);
        });

        cy.adminSaveConfiguration();

        cy.log('Checking for error messages');
        const error = validation.digits.error;
        Object.keys(validate._group.couponCodes._input).forEach(field => {
            const selector = validation.digits._error + validate._group.couponCodes._input[field].replace(/^\#/, "");
            cy.get(selector).should('include.text', error);
        });
    });
});
