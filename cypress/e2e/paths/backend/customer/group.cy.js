import {paths} from "../../../../support/paths";
import {validation} from "../../../../support/validation";

const route = paths.backend.customers.groups

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminTestRoute(route);
    });

    it(`tests`, () => {
        cy.log('Checking for error messages');
    });
});