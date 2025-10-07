const test = cy.testBackendCmsPage.config;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        test.index.clickGridRow();
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        test.index.clickAdd();
        validation.pageElements(test, test.new);
    });

    it('tests to disable a CMS page that is used in config', () => {
        test.index.clickGridRow();

        test.edit.disablePage();
        test.edit.clickSaveAndContinue();

        validation.hasWarningMessage('Cannot disable page, it is used in configuration');
        validation.hasSuccessMessage('The page has been saved.');

        cy.get('#messages').screenshot('error-disable-active-page', { overwrite: true, padding: 10 });
    });

    it('tests to delete a CMS page that is used in config', () => {
        test.index.clickGridRow();
        test.edit.clickDelete();

        validation.hasErrorMessage('Cannot delete page');

        cy.get('#messages').screenshot('error-delete-active-page', { overwrite: true, padding: 10 });
    });

    it('tests to add a CMS page', () => {
        test.index.clickAdd();
        test.edit.clickSaveAndContinue();

        // @todo add validation for required fields
    });

    it('tests to unassign a CMS page that is used in config', () => {
        test.index.clickGridRow();

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