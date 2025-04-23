const route = cy.testRoutes.backend.system.manage_curreny

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(route);
    });

    it(`tests classes and title`, () => {
        cy.adminTestRoute(route);
    });

    it(`tests empty currency`, () => {
        cy.get(route.__validation._input.from).clear();
        cy.openmage.validation.saveAction(route._buttonSave);
        cy.get('.warning-msg').should('include.text', 'Invalid input data for USD => EUR rate');
        cy.get('.success-msg').should('include.text', 'All valid rates have been saved.');
    });

    it(`tests string currency`, () => {
        cy.get(route.__validation._input.from).clear().type('abc');
        cy.openmage.validation.saveAction(route._buttonSave);
        cy.get('.warning-msg').should('include.text', 'Invalid input data for USD => EUR rate');
        cy.get('.success-msg').should('include.text', 'All valid rates have been saved.');
    });
});