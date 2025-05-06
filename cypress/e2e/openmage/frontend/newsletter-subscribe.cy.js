const route = cy.testRoutes.frontend.homepage;

describe('Check newsletter subribe', () => {
    beforeEach('Go to page', () => {
        cy.visit(route.url);
    });

    it('tests empty input', () => {
        const error = cy.openmage.validation.requiredEntry.error;
        cy.get(route.newsletter._id).should('have.value', '');
        cy.get(route.newsletter._buttonSubmit).click();
        cy.get('#advice-required-entry-newsletter').should('include.text', error);
    })

    it('Test valid input twice', () => {
        const email = cy.openmage.tools.generateRandomEmail();
        cy.log('Test first valid input');
        cy.get(route.newsletter._id).type(email).should('have.value', email);
        cy.get(route.newsletter._buttonSubmit).click();
        cy.get(cy.openmage.validation._successMessage).should('include.text', 'Thank you for your subscription.');

        cy.log('Test second valid input');
        cy.get(route.newsletter._id).type(email).should('have.value', email);
        cy.get(route.newsletter._buttonSubmit).click();
        cy.get(cy.openmage.validation._errorMessage).should('include.text', 'There was a problem with the subscription: This email address is already registered.');
    })
})
