const test = cy.openmage.test.backend.system.config.customer.promo.config;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.section.title}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.section);
        cy.openmage.admin.goToSection(test.section);
    });

    it(`tests invalid string input`, () => {
        const fieldset = test.section.couponCodes;
        cy.get('body').then($body => {
            if (!$body.find(fieldset._).hasClass('open')) {
                cy.get(fieldset._).click({ force: true });
            }
        });

        const validate = validation.digits;
        validation.fillFields(fieldset, validate, validation.test.string);
        cy.openmage.test.backend.system.config.clickSave();
        validation.validateFields(fieldset, validate);
    });
});
