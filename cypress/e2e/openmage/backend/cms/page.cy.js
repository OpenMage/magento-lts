const test = cy.openmage.test.backend.cms.page.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const utils = cy.openmage.utils;
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

        test.new.__buttons.saveAndContinue.click();
        // TODO: see https://github.com/OpenMage/magento-lts/pull/5281
        // validation.hasSuccessMessage();
        // validation.hasErrorMessage();
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'desc');
    });

    it(`tests edit route`, () => {
        tools.grid.clickFirstRow(test.index);
        validation.pageElements(test, test.edit);

        test.edit.__buttons.reset.click(test.edit.url);
        test.edit.__buttons.back.click(test.index.url);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click(test.new.url);
        test.new.__buttons.back.click(test.index.url);
    });

    it('tests to add a CMS page', () => {
        test.index.__buttons.add.click();
        test.edit.__buttons.saveAndContinue.click();

        // @todo add validation for required fields
    });

    it('tests to disable a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');

        test.edit.disablePage();
        test.edit.__buttons.saveAndContinue.click();

        validation.hasWarningMessage('You cannot disable this page as it is used to configure');
        validation.hasSuccessMessage('The page has been saved.');
    });

    it('tests to delete a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');

        test.edit.__buttons.delete.click();
        validation.hasErrorMessage('You cannot delete this page as it is used to configure');
    });

    it('tests to unassign a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');

        // TODO: fix needed - this test passes because of a Magento bug
        // TODO: update sample data
        //cy.log('Assign another store to the CMS page');
        //cy.get(test.edit.__fields.page_store_id.selector)
        //    .select(4);
        //test.edit.__buttons.saveAndContinue.click();
        //validation.hasSuccessMessage(message);
        //utils.screenshot(cy.get('#messages'), 'cms.page.unassignActivePage');

        test.edit.resetStores();
        test.edit.__buttons.saveAndContinue.click();
        validation.hasSuccessMessage('The page has been saved.');
    });
});