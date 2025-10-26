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
        test.index.__buttons.add.click();
        validation.removeClasses(test.new);

        //const message = 'An error occurred while saving this configuration: The priority must be between 0 and 1.';
        test.new.__buttons.saveAndContinue.click();
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

        test.edit.__buttons.reset.click();
        cy.url().should('include', test.edit.url);

        test.edit.__buttons.back.click();
        cy.url().should('include', test.index.url);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click();
        cy.url().should('include', test.new.url);

        test.new.__buttons.back.click();
        cy.url().should('include', test.index.url);
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

        const success = 'The page has been saved.';
        const warning = 'You cannot disable this page as it is used to configure';
        validation.hasWarningMessage(warning);
        validation.hasSuccessMessage(success);
        utils.screenshot(cy.openmage.validation._messagesContainer, 'message.cms.page.disableActivePage');
    });

    it('tests to delete a CMS page that is used in config', () => {
        test.index.clickGridRow('no-route');
        test.edit.__buttons.delete.click();

        const message = 'You cannot delete this page as it is used to configure';
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
        //test.edit.__buttons.saveAndContinue.click();
        //validation.hasSuccessMessage(message);
        //utils.screenshot(cy.get('#messages'), 'cms.page.unassignActivePage');

        test.edit.resetStores();
        test.edit.__buttons.saveAndContinue.click();
        validation.hasSuccessMessage(message);
    });
});