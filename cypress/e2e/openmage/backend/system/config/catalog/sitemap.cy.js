const test = cy.testBackendSystemConfig.config.catalog.sitemap;
const saveButton = cy.testBackendSystemConfig.config._buttonSave;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGetConfiguration(test);
    });

    const priority = test.__validation.priority._input;

    it(`tests invalid string priority`, () => {
        validation.fillFields(priority, validation.number, validation.test.string);
        tools.clickAction(saveButton);
        validation.validateFields(priority, validation.number);
    });

    it(`tests invalid number priority`, () => {
        validation.fillFields(priority, validation.numberRange, validation.test.numberGreater1);
        tools.clickAction(saveButton);
        validation.validateFields(priority, validation.numberRange);
     });

    it(`tests empty priority`, () => {
        validation.fillFields(priority, validation.requiredEntry);
        tools.clickAction(saveButton);
        validation.validateFields(priority, validation.requiredEntry);
    });

    it(`tests empty priority, no js`, () => {
        const error = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';
        validation.fillFields(priority, validation.requiredEntry);
        validation.removeClasses(priority);
        tools.clickAction(saveButton);
        validation.hasErrorMessage(error);
    });
});