const test = cy.openmage.test.backend.system.design.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.__buttons.add.click();
        validation.removeClasses(test.new);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        const success = 'The design change has been saved.';
        const screenshot = 'message.system.design.saveEmptyWithoutJs-1';
        test.new.__buttons.save.click();
        validation.hasSuccessMessage(success,{ match: 'have.text', screenshot: true, filename: screenshot });
    });

    it(`tests save empty values, no js, 2nd time`, () => {
        test.index.__buttons.add.click();
        validation.removeClasses(test.new);

        // TODO: Clicking "Save" instead of "Save and Continue" because not implemented in this section
        const error = 'Your design change for the specified store intersects with another one, please specify another date range.';
        const screenshot = 'message.system.design.saveEmptyWithoutJs-2';
        test.new.__buttons.save.click();
        validation.hasErrorMessage(error, { match: 'have.text', screenshot: true, filename: screenshot });
    });

    it(`tests delete last added design`, () => {
        tools.grid.clickFirstRow(test.index);

        const success = 'The design change has been deleted.';
        const screenshot = 'message.system.design.deleteLastAddedDesign';
        test.edit.__buttons.delete.click();
        validation.hasSuccessMessage(success, { match: 'have.text', screenshot: true, filename: screenshot });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        // TODO: There is no edit route for design updates, update sample data?
        validation.pageElements(test, test.index);

        //test.edit.__buttons.reset.click();
        //cy.url().should('include', test.edit.url);

        //test.edit.__buttons.back.click();
        //cy.url().should('include', test.index.url);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click();
        cy.url().should('include', test.new.url);

        test.new.__buttons.back.click();
        cy.url().should('include', test.index.url);
    });
});
