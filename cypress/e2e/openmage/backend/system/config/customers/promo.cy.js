const route = cy.testRoutes.backend.system.config.customers.promo;
const saveButton = cy.testRoutes.backend.system.config._buttonSave;
const validation = cy.openmage.validation;

describe(`Checks admin system "${route.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string input`, () => {
        const fieldset = route.__validation.__groups.couponCodes;
        cy.get('body').then($body => {
            if (!$body.find(fieldset._id).hasClass('open')) {
                cy.get(fieldset._id).click({force: true});
            }
        });

        const fields = fieldset._input;
        const validate = validation.digits;
        validation.fillFields(fields, validate, validation.test.string);
        validation.saveAction(saveButton);
        validation.validateFields(fields, validate);
    });
});
