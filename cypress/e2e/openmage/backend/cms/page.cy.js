const test = cy.testBackendCmsPage.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        const error = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';

        test.index.clickAdd();
        validation.removeClasses(test.new);

        test.new.clickSaveAndContinue();
        //validation.hasErrorMessage(error);
        // screenshot with error message
        cy.get('body').screenshot('saveEmptyWithoutJs.message.cms.page', { overwrite: true, padding: 10 });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        tools.grid.clickFirstRow(test.index);
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        test.index.clickAdd();
        validation.pageElements(test, test.new);
    });

    it('tests to add a CMS page', () => {
        test.index.clickAdd();
        test.edit.clickSaveAndContinue();

        // @todo add validation for required fields
    });

    it('tests to disable a CMS page that is used in config', () => {
        test.index.clickGridRow('td', 'no-route', 'Select "no-route"" CMS page');

        test.edit.disablePage();
        test.edit.clickSaveAndContinue();

        validation.hasWarningMessage('Cannot disable page, it is used in configuration');
        validation.hasSuccessMessage('The page has been saved.');
        cy.get('#messages').screenshot('cms.page.disableActivePage', { overwrite: true, padding: 10 });
    });

    it('tests to delete a CMS page that is used in config', () => {
        test.index.clickGridRow('td', 'no-route', 'Select "no-route"" CMS page');
        test.edit.clickDelete();

        validation.hasErrorMessage('Cannot delete page');
        cy.get('#messages').screenshot('cms.page.deleteActivePage', { overwrite: true, padding: 10 });
    });

    it('tests to unassign a CMS page that is used in config', () => {
        test.index.clickGridRow('td', 'no-route', 'Select "no-route"" CMS page');

        //cy.log('Assign another store to the CMS page');
        //cy.get(test.edit.__fields.page_store_id.selector)
        //    .select(4);

        //test.edit.clickSaveAndContinue();

        // @todo: fix needed - this test passes because of a Magento bug
        //validation.hasSuccessMessage('The page has been saved.');

        test.edit.resetStores();
        test.edit.clickSaveAndContinue();

        validation.hasSuccessMessage('The page has been saved.');
    });
});