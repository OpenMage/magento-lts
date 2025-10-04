const route = cy.testRoutes.backend.cms.page;
const validation = cy.openmage.validation;

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(route);
    });

    it(`tests classes and title`, () => {
        cy.adminTestRoute(route);
    });

    it('tests to disable a CMS page that is used in config', () => {
        cy.log('Select a CMS page');
        cy.get(route._gridTable)
            .contains('td', 'no-route')
            .click();

        cy.log('Disable the CMS page');
        cy.get('#page_is_active')
            .select('Disabled');

        validation.saveAction(route._buttonSaveAndContinue);
        cy.get(validation._warningMessage).should('include.text', 'Cannot disable page, it is used in configuration');
        cy.get(validation._successMessage).should('include.text', 'The page has been saved.');
    });
});