const test = cy.testFrontend.homepage;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe('Check newsletter subscribe', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.url);
    });

    it('tests empty input', () => {
        const error = validation.requiredEntry.error;
        cy.get(test.newsletter._id).should('have.value', '');
        tools.clickAction(test.newsletter._buttonSubmit);
        cy.get('#advice-required-entry-newsletter').should('include.text', error);
    })

    it('Test valid input twice', () => {
        const email = tools.generateRandomEmail();
        cy.log('Test first valid input');
        cy.get(test.newsletter._id).type(email).should('have.value', email);
        tools.clickAction(test.newsletter._buttonSubmit);
        validation.hasSuccessMessage('Thank you for your subscription.');

        cy.log('Test second valid input');
        cy.get(test.newsletter._id).type(email).should('have.value', email);
        tools.clickAction(test.newsletter._buttonSubmit);
        validation.hasErrorMessage('There was a problem with the subscription: This email address is already registered.');
    })
})
