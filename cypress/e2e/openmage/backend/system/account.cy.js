const test = cy.openmage.test.backend.system.account.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
        cy.fixture(test.__fixture).as('fixture');
    });

    it(`tests save empty, no js`, function() {
        validation.fixture.fillFields(this.fixture.default, true);
        validation.fixture.removeClasses(this.fixture.default);

        tools.admin.buttons.clickSave(test.index.url);
        validation.hasErrorMessage('Current password field cannot be empty.');

        /// with filling password
        validation.fixture.fillFields(this.fixture.default, true);
        validation.fixture.removeClasses(this.fixture.default);

        cy.getBySel(this.fixture.default.currentPassword._)
            .type(this.fixture.default.currentPassword.value)
            .should('have.value', this.fixture.default.currentPassword.value);

        tools.admin.buttons.clickSave(test.index.url);
        validation.hasErrorMessage('User Name is required field.');
        validation.hasErrorMessage('First Name is required field.');
        validation.hasErrorMessage('Last Name is required field.');
        validation.hasErrorMessage('Please enter a valid email.');
   });

    it(`tests save empty input`, function () {
        validation.fixture.fillFields(this.fixture.default, true);
        tools.admin.buttons.clickSave();
        validation.fixture.validateFields(this.fixture.default, validation.requiredEntry);
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });
});
