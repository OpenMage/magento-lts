const route = cy.testRoutes.backend.system.myaccount

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogIn();
        cy.adminGoToTestRoute(route);
    });

    it(`tests classes and title`, () => {
        cy.adminTestRoute(route);
    });

    it(`tests empty input`, () => {
        const validate = cy.openmage.validation.requiredEntry;
        cy.openmage.validation.fillFields(route.__validation._input, validate);
        cy.openmage.validation.saveAction(route._buttonSave);
        cy.openmage.validation.validateFields(route.__validation._input, validate);
    });
});