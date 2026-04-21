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
        tools.admin.buttons.clickAdd();
        validation.fixture.removeClasses(this.fixture.default);

        tools.admin.buttons.clickSaveAndContinue(test.edit.url);
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

        tools.admin.buttons.clickReset(test.edit.url);
        tools.admin.buttons.clickBack(test.index.url);
    });

    it(`tests new route`, () => {
        tools.admin.buttons.clickAdd();
        validation.pageElements(test, test.new);

        tools.admin.buttons.clickReset(test.new.url);
        tools.admin.buttons.clickBack(test.index.url);
    });

    it('tests to add a CMS page', () => {
        tools.admin.buttons.clickAdd();
        tools.admin.buttons.clickSaveAndContinue(test.edit.url);

        // @todo add validation for required fields
    });

    it('tests to disable a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');

        test.edit.disablePage();
        tools.admin.buttons.clickSaveAndContinue(test.edit.url);

        validation.hasWarningMessage('You cannot disable this page as it is used to configure');
        validation.hasSuccessMessage('The page has been saved.');
    });

    it('tests to delete a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');

        tools.admin.buttons.clickDelete(test.index.url);
        validation.hasErrorMessage('You cannot delete this page as it is used to configure');
    });

    it('tests to unassign a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');

        // TODO: fix needed - this test passes because of a Magento bug
        // TODO: update sample data
        //cy.log('Assign another store to the CMS page');
        //cy.get(test.edit.__fields.page_store_id.selector)
        //    .select(4);
        //tools.admin.buttons.clickSaveAndContinue(test.edit.url);
        //validation.hasSuccessMessage(message);
        //utils.screenshot(cy.get('#messages'), 'cms.page.unassignActivePage');

        test.edit.resetStores();
        tools.admin.buttons.clickSaveAndContinue(test.edit.url);
        validation.hasSuccessMessage('The page has been saved.');
    });
});