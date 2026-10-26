const test = cy.openmage.test.backend.system.config.general.general.config;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.section.title}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.section);
        cy.openmage.admin.goToSection(test.section);
    });

    const fields = test.section.storeInformation.__fields;
    const labelEnv = '[ENV]';
    const labelStoreView = '[STORE VIEW]';

    it(`tests ENV override`, () => {
        // test overrides in default config scope
        cy.get(fields.name._).should('have.value', 'ENV name default');
        cy.get(fields.name.label).should('have.text', labelEnv);

        cy.get(fields.phone._).should('have.value', '');
        cy.get(fields.phone.label).should('have.text', labelStoreView);

        cy.get(fields.address._).should('have.value', '');
        cy.get(fields.address.label).should('have.text', labelStoreView);

        // test overrides in website scope
        cy.openmage.admin.goToConfigScope(test.section, 'website_base');
        cy.get(fields.name._).should('have.value', 'ENV name default');
        cy.get(fields.name.label).should('have.text', labelEnv);

        cy.get(fields.phone._).should('have.value', 'ENV phone website');
        cy.get(fields.phone.label).should('have.text', labelEnv);

        cy.get(fields.address._).should('have.value', '');
        cy.get(fields.address.label).should('have.text', labelStoreView);

        // test overrides in English/default store view
        cy.openmage.admin.goToConfigScope(test.section, 'store_default');
        cy.get(fields.name._).should('have.value', 'ENV name default');
        cy.get(fields.name.label).should('have.text', labelEnv);

        cy.get(fields.phone._).should('have.value', 'ENV phone website');
        cy.get(fields.phone.label).should('have.text', labelEnv);

        cy.get(fields.address._).should('have.value', '');
        cy.get(fields.address.label).should('have.text', labelStoreView);

        // test overrides in French store view
        cy.openmage.admin.goToConfigScope(test.section, 'store_french');
        cy.get(fields.name._).should('have.value', 'ENV name default');
        cy.get(fields.name.label).should('have.text', labelEnv);

        cy.get(fields.phone._).should('have.value', 'ENV phone website');
        cy.get(fields.phone.label).should('have.text', labelEnv);

        cy.get(fields.address._).should('have.value', '');
        cy.get(fields.address.label).should('have.text', labelStoreView);

        // test overrides in German store view
        cy.openmage.admin.goToConfigScope(test.section, 'store_german');
        cy.get(fields.name._).should('have.value', 'ENV name default');
        cy.get(fields.name.label).should('have.text', labelEnv);

        cy.get(fields.phone._).should('have.value', 'ENV phone website');
        cy.get(fields.phone.label).should('have.text', labelEnv);

        cy.get(fields.address._).should('have.value', 'ENV address store');
        cy.get(fields.address.label).should('have.text', labelEnv);
    });
});