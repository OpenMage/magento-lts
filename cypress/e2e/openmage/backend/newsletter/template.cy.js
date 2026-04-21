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
        tools.admin.buttons.clickAdd();
        validation.removeClassesFromInput();
        validation.removeClassesFromTextarea();

        // TODO: add save and continue functionality
        tools.admin.buttons.clickSave(test.index.url);
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

        tools.admin.buttons.clickReset(test.edit.url);
        tools.admin.buttons.clickSave(test.index.url);
        validation.hasSuccessMessage('The template has been saved.');
    });

    it(`tests new route`, () => {
        tools.admin.buttons.clickAdd();
        validation.pageElements(test, test.new);

        tools.admin.buttons.clickReset(test.new.url);
        tools.admin.buttons.clickBack(test.index.url);
    });
});
