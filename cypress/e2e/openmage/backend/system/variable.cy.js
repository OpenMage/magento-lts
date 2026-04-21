const test = cy.openmage.test.backend.system.variable.config;
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

        tools.admin.buttons.clickSaveAndContinue(test.edit.url);
        validation.hasErrorMessage('Validation has failed.', { match: 'have.text' });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'desc');
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        tools.admin.buttons.clickReset(test.new.url);
        tools.admin.buttons.clickBack(test.index.url);
    });

    it(`tests edit route`, () => {
        // TODO: There is no sample data for custom variables, need to create one first
        validation.pageElements(test, test.index);

        //tools.admin.buttons.clickReset(test.edit.url);
        //tools.admin.buttons.clickBack(test.index.url);
    });
});
