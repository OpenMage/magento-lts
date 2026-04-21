const test = cy.openmage.test.backend.system.email.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
        cy.fixture(test.__fixture).as('fixture');
    });

    it(`tests save empty values, no js`, function () {
        test.index.__buttons.add.click();
        validation.fixture.removeClasses(this.fixture.default);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        tools.admin.buttons.clickSave(test.index.url);
        validation.hasErrorMessage('The template Name must not be empty.', { match: 'have.text' });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'desc');
    });

    it(`tests edit route`, () => {
        // TODO: There is no edit route for email templates
        validation.pageElements(test, test.index);

        //tools.admin.buttons.clickReset(test.edit.url);
        //tools.admin.buttons.clickBack(test.index.url);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        tools.admin.buttons.clickReset(test.new.url);
        tools.admin.buttons.clickBack(test.index.url);
    });
});
