const route = cy.testRoutes.backend.system.config.catalog.sitemap;
const assert = cy.openmage.validation.assert;

describe(`Checks admin system "${route.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogInValidUser();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string priority`, () => {
        const validate = cy.openmage.validation.number;
        cy.openmage.validation.fillFields(route.__validation.priority._input, validate, assert.string);
        cy.openmage.validation.saveAction(route._buttonSave);
        cy.openmage.validation.validateFields(route.__validation.priority._input, validate);
    });

    it(`tests invalid number priority`, () => {
        const validate = cy.openmage.validation.numberRange;
        cy.openmage.validation.fillFields(route.__validation.priority._input, validate, assert.numberGreater1);
        cy.openmage.validation.saveAction(route._buttonSave);
        cy.openmage.validation.validateFields(route.__validation.priority._input, validate);
     });

    it(`tests empty priority`, () => {
        const validate = cy.openmage.validation.requiredEntry;
        cy.openmage.validation.fillFields(route.__validation.priority._input, validate);
        cy.openmage.validation.saveAction(route._buttonSave);
        cy.openmage.validation.validateFields(route.__validation.priority._input, validate);
    });
});