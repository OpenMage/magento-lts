const test = cy.testBackendCmsPage.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(test, test.index);
    });

    it(`tests index route`, () => {
        check.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        tools.clickContains(test.index._grid, 'td', 'no-route', 'Select a CMS page');
        check.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        tools.click(test.index.__buttons.add);
        check.pageElements(test, test.new);
    });

    it('tests to disable a CMS page that is used in config', () => {
        tools.clickContains(test.index._grid, 'td', 'no-route', 'Select a CMS page');

        test.edit.disablePage();

        tools.click(test.edit.__buttons.saveAndContinue);
        validation.hasWarningMessage('Cannot disable page, it is used in configuration');
        validation.hasSuccessMessage('The page has been saved.');
        cy.get('#messages').screenshot('error-disable-active-page', { overwrite: true});
    });

    it('tests to delete a CMS page that is used in config', () => {
        tools.clickContains(test.index._grid, 'td', 'no-route', 'Select a CMS page');

        tools.click(test.edit.__buttons.delete);
        validation.hasErrorMessage('Cannot delete page');
        cy.get('#messages').screenshot('error-delete-active-page', { overwrite: true});
    });

    it('tests to add a CMS page', () => {
        tools.click(test.index.__buttons.add);
        tools.click(test.edit.__buttons.saveAndContinue);

        // @todo add validation for required fields
    });

    it('tests to un-asign a CMS page that is used in config', () => {
        tools.clickContains(test.index._grid, 'td', 'no-route', 'Select a CMS page');

        //cy.log('Asign another store to the CMS page');
        //cy.get('#page_store_id')
        //    .select(4);

        //tools.clickAction(test.edit.__buttons.saveAndContinue);

        // @todo: fix needed - this test passes because of a Magento bug
        //validation.hasSuccessMessage('The page has been saved.');

        test.edit.resetStores();

        tools.click(test.edit.__buttons.saveAndContinue);
        validation.hasSuccessMessage('The page has been saved.');
    });

});