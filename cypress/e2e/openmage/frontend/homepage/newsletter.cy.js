const test = cy.openmage.test.frontend.homepage.newsletter.config;
const tools = cy.openmage.tools;
const utils = cy.openmage.utils;
const validation = cy.openmage.validation;

describe('Check newsletter subscribe', () => {
    beforeEach('Go to page', () => {
        cy.visit(test.url);
    });

    it('tests empty input', () => {
        const error = validation.requiredEntry.error;
        cy.get(test._id).should('have.value', '');
        tools.click(test._buttonSubmit);
        cy.get('#advice-required-entry-newsletter').should('include.text', error);
    })

    it('Test valid input twice', () => {
        const email = utils.generateRandomEmail();
        const success = 'Thank you for your subscription.';
        cy.log('Test first valid input');
        cy.get(test._id).type(email).should('have.value', email);
        tools.click(test._buttonSubmit);
        validation.hasSuccessMessage(success, {screenshot: false, filename: 'message.newsletter.subscribe.success'});

        const error = 'There was a problem with the subscription: This email address is already registered.';
        cy.log('Test second valid input');
        cy.get(test._id).type(email).should('have.value', email);
        tools.click(test._buttonSubmit);
        validation.hasErrorMessage(error, {screenshot: false, filename: 'message.newsletter.subscribe.alreadyRegistered'});
    })
})
