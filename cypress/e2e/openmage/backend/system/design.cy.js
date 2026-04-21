const test = cy.openmage.test.backend.system.design.config;
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
        // TODO: see https://github.com/OpenMage/magento-lts/pull/5281
        validation.hasSuccessMessage();
        // validation.hasErrorMessage();
    });

    it(`tests save empty values, no js, 2nd time`, function () {
        test.index.__buttons.add.click();
        validation.fixture.removeClasses(this.fixture.default);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        tools.admin.buttons.clickSave(test.index.url);
        validation.hasErrorMessage('Your design change for the specified store intersects with another one, please specify another date range.', { match: 'have.text' });
    });

    it(`tests delete last added design`, () => {
        tools.grid.clickFirstRow(test.index);

        test.edit.__buttons.delete.click();
        validation.hasSuccessMessage('The design change has been deleted.', { match: 'have.text' });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'desc');
    });

    it(`tests edit route`, () => {
        // TODO: There is no edit route for design updates, update sample data?
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
