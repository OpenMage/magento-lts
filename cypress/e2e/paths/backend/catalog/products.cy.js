import {paths} from "../../../../support/paths";
import {validation} from "../../../../support/validation";

const route = paths.backend.catalog.products

describe(`Checks admin system "${route.h3}"`, () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminTestRoute(route);
    });

    it(`tests`, () => {
        cy.log('Checking for error messages');
    });

    it(`tests filter options`, () => {
        cy.log('Checking for the number of filter type options');
        cy.get('#productGrid_product_filter_type option').should('have.length', 7);

        cy.log('Checking for the number of filter visibility options');
        cy.get('#productGrid_product_filter_visibility option').should('have.length', 5);
    });

    it(`tests add simple product`, () => {
        cy.log('Checking for the number of add product options');
        cy.get('#productGrid_product_add_product option').should('have.length', 6);

        cy.log('Checking for the number of add product options');
        cy.get('#productGrid_product_add_product option').contains('Simple Product').click();
        cy.get('#productGrid_product_add_product option').should('have.length', 5);
    });
});