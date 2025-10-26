const test = cy.openmage.test.backend.customer.group.config;
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
        // TODO fix it
        const message = 'The customer group has been saved.';
        const screenshot = 'message.customer.groups.saveEmptyWithoutJs';
        test.new.__buttons.save.click();
        validation.hasSuccessMessage(message, { screenshot: true, filename: screenshot });
    });

    // TODO: add test for save with values

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests edit route`, () => {
        test.index.clickGridRow('Wholesale');
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
