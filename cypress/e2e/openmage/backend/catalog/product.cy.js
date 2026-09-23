const test = cy.openmage.test.backend.catalog.product.config;
const check = cy.openmage.check;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
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
        tools.admin.buttons.clickBack(test.index.url);
    });

    it(`tests new route`, () => {
        tools.admin.buttons.clickAdd();
        validation.pageElements(test, test.new);

        tools.admin.buttons.clickReset(test.new.url);
        tools.admin.buttons.clickBack(test.index.url);
    });

    it(`tests filter options`, () => {
        cy.log('Checking for the number of filter type options');
        cy.get('#productGrid_product_filter_type option').should('have.length', 7);

        cy.log('Checking for the number of filter visibility options');
        cy.get('#productGrid_product_filter_visibility option').should('have.length', 5);
    });
});
