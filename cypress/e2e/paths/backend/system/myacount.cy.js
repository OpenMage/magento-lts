import {paths} from "../../../../support/paths";
import {validation} from "../../../../support/validation";

const route = paths.backend.system.myaccount
const validate = {
    _input: {
        username: '#username',
        firstname: '#firstname',
        lastname: '#lastname',
        email: '#email',
        current_password: '#current_password',
    }
}

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.adminLogInValidUser();
        cy.adminGoToTestRoute(route);
    });

    it(`tests classes and title`, () => {
        cy.adminTestRoute(route);
    });

    it(`tests empty priority`, () => {
        Object.keys(validate._input).forEach(field => {
            const selector = validate._input[field];

            cy
                .get(selector)
                .clear({ force: true })
                .should('have.value', '');
        });

        cy.log('Clicking on Save button');
        cy.get('.form-buttons button[title="Save Account"]').click({force: true, multiple: true});

        cy.log('Checking for error messages');
        const error = validation.errors.requiredEntry;
        cy.get('#advice-required-entry-username').should('include.text', error);
        cy.get('#advice-required-entry-firstname').should('include.text', error);
        cy.get('#advice-required-entry-lastname').should('include.text', error);
        cy.get('#advice-required-entry-email').should('include.text', error);
        cy.get('#advice-required-entry-current_password').should('include.text', error);
    });
});