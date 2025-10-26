const test = cy.openmage.test.backend.cms.block.config;
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

        // TODO: do not save empty block, show error instead
        const message = 'The block has been saved.';
        const screenshot = 'message.cms.block.saveEmptyWithoutJs';
        test.new.__buttons.saveAndContinue.click();
        validation.hasSuccessMessage(message, { match: 'have.text', screenshot: true, filename: screenshot });
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
});
