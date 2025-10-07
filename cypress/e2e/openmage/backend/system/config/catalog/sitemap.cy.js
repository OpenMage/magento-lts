const test = cy.testBackendSystemConfig.config.catalog.sitemap;
const saveButton = cy.testBackendSystemConfig.config._buttonSave;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.h3}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGetConfiguration(test);
    });

    const fields = test.priority.__fields;

    it(`tests save empty, no js`, () => {
        validation.fillFields(fields, validation.requiredEntry);
        validation.removeClasses(fields);

        const error = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';
        tools.click(saveButton);
        validation.hasErrorMessage(error);
    });

    it(`tests invalid string priority`, () => {
        validation.fillFields(fields, validation.number, validation.test.string);
        tools.click(saveButton);
        validation.validateFields(fields, validation.number);
    });

    it(`tests invalid number priority`, () => {
        validation.fillFields(fields, validation.numberRange, validation.test.numberGreater1);
        tools.click(saveButton);
        validation.validateFields(fields, validation.numberRange);
     });

    it(`tests empty priority`, () => {
        validation.fillFields(fields, validation.requiredEntry);
        tools.click(saveButton);
        validation.validateFields(fields, validation.requiredEntry);
    });
});