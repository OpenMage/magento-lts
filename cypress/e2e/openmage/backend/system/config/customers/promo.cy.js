const test = cy.testBackendSystem.config.customers.promo;
const saveButton = cy.testBackendSystem.config._buttonSave;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGetConfiguration(test);
    });

    it(`tests invalid string input`, () => {
        const fieldset = test.__validation.__groups.couponCodes;
        cy.get('body').then($body => {
            if (!$body.find(fieldset._id).hasClass('open')) {
                cy.get(fieldset._id).click({force: true});
            }
        });

        const fields = fieldset._input;
        const validate = validation.digits;
        validation.fillFields(fields, validate, validation.test.string);
        tools.clickAction(saveButton);
        validation.validateFields(fields, validate);
    });
});
