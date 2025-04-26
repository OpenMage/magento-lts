const route = cy.testRoutes.backend.system.config.catalog.sitemap;
const saveButton = cy.testRoutes.backend.system.config._buttonSave
const validation = cy.openmage.validation;

describe(`Checks admin system "${route.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string priority`, () => {
        validation.fillFields(route.__validation.priority._input, validation.number, validation.assert.string);
        validation.saveAction(saveButton);
        validation.validateFields(route.__validation.priority._input, validation.number);
    });

    it(`tests invalid number priority`, () => {
        validation.fillFields(route.__validation.priority._input, validation.numberRange, validation.assert.numberGreater1);
        validation.saveAction(saveButton);
        validation.validateFields(route.__validation.priority._input, validation.numberRange);
     });

    it(`tests empty priority`, () => {
        validation.fillFields(route.__validation.priority._input, validation.requiredEntry);
        validation.saveAction(saveButton);
        validation.validateFields(route.__validation.priority._input, validation.requiredEntry);
    });

    it(`tests empty priority, no js`, () => {
        const error = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';
        validation.fillFields(route.__validation.priority._input, validation.requiredEntry);
        validation.removeClasses(route.__validation.priority._input);
        validation.saveAction(saveButton);
        cy.get(cy.openmage.validation._errorMessage).should('include.text', error);
    });
});