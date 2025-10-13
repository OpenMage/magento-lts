const test = cy.openmage.test.backend.cms.page.config;
const tools = cy.openmage.tools;
const utils = cy.openmage.utils;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.clickAdd();
        validation.removeClasses(test.new);

        //const message = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';
        test.new.clickSaveAndContinue();
        // TODO: fix it
        //validation.hasErrorMessage(message);
        // screenshot with error message
        utils.screenshot('body', 'message.cms.page.saveEmptyWithoutJs');
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
        test.index.clickGridRow('no-route');

        test.edit.disablePage();
        test.edit.clickSaveAndContinue();

        const success = 'The page has been saved.';
        const warning = 'Cannot disable page, it is used in configuration';
        validation.hasWarningMessage(warning);
        validation.hasSuccessMessage(success);
        utils.screenshot(cy.openmage.validation._messagesContainer, 'message.cms.page.disableActivePage');
    });

    it('tests to delete a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');
        test.edit.clickDelete();

        const message = 'Cannot delete page';
        const screenshot = 'message.cms.page.deleteActivePage';
        validation.hasErrorMessage(message, { screenshot: true, filename: screenshot });
    });

    it('tests to unassign a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');

        // TODO: fix needed - this test passes because of a Magento bug
        // TODO: update sample data
        const message = 'The page has been saved.';
        //cy.log('Assign another store to the CMS page');
        //cy.get(test.edit.__fields.page_store_id.selector)
        //    .select(4);
        //test.edit.clickSaveAndContinue();
        //validation.hasSuccessMessage(message);
        //utils.screenshot(cy.get('#messages'), 'cms.page.unassignActivePage');

        test.edit.resetStores();
        test.edit.clickSaveAndContinue();
        validation.hasSuccessMessage(message);
    });
});