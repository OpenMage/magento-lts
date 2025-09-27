const route = cy.testRoutes.backend.system.config.catalog.sitemap;
const saveButton = cy.testRoutes.backend.system.config._buttonSave;
const validation = cy.openmage.validation;

describe(`Checks admin system "${route.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGetConfiguration(route);
    });

    const priority = route.__validation.priority._input;

    it(`tests invalid string priority`, () => {
        validation.fillFields(priority, validation.number, validation.test.string);
        validation.saveAction(saveButton);
        validation.validateFields(priority, validation.number);
    });

    it(`tests invalid number priority`, () => {
        validation.fillFields(priority, validation.numberRange, validation.test.numberGreater1);
        validation.saveAction(saveButton);
        validation.validateFields(priority, validation.numberRange);
     });

    it(`tests empty priority`, () => {
        validation.fillFields(priority, validation.requiredEntry);
        validation.saveAction(saveButton);
        validation.validateFields(priority, validation.requiredEntry);
    });

    it(`tests empty priority, no js`, () => {
        const error = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';
        validation.fillFields(priority, validation.requiredEntry);
        validation.removeClasses(priority);
        validation.saveAction(saveButton);
        cy.get(cy.openmage.validation._errorMessage).should('include.text', error);
    });
});