const test = cy.openmage.test.backend.system.variable.config;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.__buttons.add.click();
        validation.removeClasses(test.new);

        const message = 'Validation has failed.';
        const screenshot = 'message.system.variable.saveEmptyWithoutJs';
        test.new.__buttons.saveAndContinue.click();
        validation.hasErrorMessage(message, { match: 'have.text', screenshot: true, filename: screenshot });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click();
        cy.url().should('include', test.new.url);

        test.new.__buttons.back.click();
        cy.url().should('include', test.index.url);
    });

    it(`tests edit route`, () => {
        // TODO: There is no sample data for custom variables, need to create one first
        validation.pageElements(test, test.index);

        //test.edit.__buttons.reset.click();
        //cy.url().should('include', test.edit.url);

        //test.edit.__buttons.back.click();
        //cy.url().should('include', test.index.url);
    });
});
