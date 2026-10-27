const test = cy.openmage.test.backend.promo.quote.config;
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
        validation.removeClassesAll();

        tools.admin.buttons.clickSaveAndContinue(test.edit.url);
        // TODO: see https://github.com/OpenMage/magento-lts/pull/5281
        validation.hasSuccessMessage();
        // validation.hasErrorMessage();
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);

        tools.grid.clickSortedColumn(test.index);
        cy.openmage.admin.goToPage(test, test.index);
        check.gridSort(test, test.index, 'desc');
    });

    it(`tests edit route`, () => {
        tools.grid.clickFirstRow(test.index);
        validation.pageElements(test, test.edit);
    });

    it(`tests new route`, () => {
        tools.admin.buttons.clickAdd();
        validation.pageElements(test, test.new);
    });
});
