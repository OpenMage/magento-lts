const test = cy.openmage.test.backend.newsletter.template.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests save empty values, no js`, () => {
        test.index.__buttons.add.click();
        validation.removeClassesFromInput();
        validation.removeClassesFromTextarea();

        // TODO: add save and continue functionality
        test.new.__buttons.save.click();
        validation.hasErrorMessage('You must give a non-empty value for field \'template_code\'.', { match: 'have.text' });
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'asc');
    });

    it(`tests edit route`, () => {
        tools.grid.clickFirstRow(test.index);
        validation.pageElements(test, test.edit);

        test.edit.__buttons.reset.click(test.edit.url);
        test.edit.__buttons.save.click(test.index.url);
        validation.hasSuccessMessage('The template has been saved.');
    });

    it(`tests new route`, () => {
        test.index.__buttons.add.click();
        validation.pageElements(test, test.new);

        test.new.__buttons.reset.click(test.new.url);
        test.new.__buttons.back.click(test.index.url);
    });
});
