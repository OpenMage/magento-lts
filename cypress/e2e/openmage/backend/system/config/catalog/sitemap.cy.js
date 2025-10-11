const test = cy.openmage.test.backend.system.config.catalog.sitemap.config;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.section.title}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.section);
        cy.openmage.admin.goToSection(test.section);
    });

    const fields = test.section.priority;

    it(`tests save empty values, no js`, () => {
        validation.fillFields(fields, validation.requiredEntry);
        validation.removeClasses(fields);

        const message = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';
        const screenshot = 'message.sytem.config.catalog.sitemap.saveEmptyWithoutJs';
        cy.openmage.test.backend.system.config.clickSave();
        validation.hasErrorMessage(message, { screenshot: true, filename: screenshot });
    });

    it(`tests invalid string priority`, () => {
        validation.fillFields(fields, validation.number, validation.test.string);
        cy.openmage.test.backend.system.config.clickSave();
        validation.validateFields(fields, validation.number);
    });

    it(`tests invalid number priority`, () => {
        validation.fillFields(fields, validation.numberRange, validation.test.numberGreater1);
        cy.openmage.test.backend.system.config.clickSave();
        validation.validateFields(fields, validation.numberRange);
     });

    it(`tests empty priority`, () => {
        validation.fillFields(fields, validation.requiredEntry);
        cy.openmage.test.backend.system.config.clickSave();
        validation.validateFields(fields, validation.requiredEntry);
    });
});