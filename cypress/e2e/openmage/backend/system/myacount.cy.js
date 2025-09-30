const route = cy.testRoutes.backend.system.myaccount;
const validation = cy.openmage.validation;

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
        validation.fillFields(route.__validation._input, validate);
        validation.saveAction(route._buttonSave);
        validation.validateFields(route.__validation._input, validate);
    });
});