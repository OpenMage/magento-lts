const route = cy.testRoutes.backend.system.manage_curreny;
const validation = cy.openmage.validation;

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(route);
    });

    it(`tests classes and title`, () => {
        cy.adminTestRoute(route);
    });

    const warning = 'Invalid input data for USD => EUR rate';
    const success = 'All valid rates have been saved.';

    it(`tests empty currency`, () => {
        cy.get('body').then($body => {
            if ($body.find(route.__validation._input.from).length > 0) {
                cy.get(route.__validation._input.from).clear();
                validation.saveAction(route._buttonSave);
                cy.get(validation._warningMessage).should('include.text', warning);
                cy.get(validation._successMessage).should('include.text', success);
            }
        });
    });

    it(`tests string currency`, () => {
        cy.get('body').then($body => {
            if ($body.find(route.__validation._input.from).length > 0) {
                cy.get(route.__validation._input.from).clear().type('abc');
                validation.saveAction(route._buttonSave);
                cy.get(validation._warningMessage).should('include.text', warning);
                cy.get(validation._successMessage).should('include.text', success);
            }
        });
    });
});