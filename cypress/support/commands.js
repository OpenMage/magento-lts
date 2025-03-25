const credentials = {
    Admin: {
        login: 'admin',
        password: 'veryl0ngpassw0rd',
    },
    NoAdmin: {
        login: 'user',
        password: 'password'
    }
};

Cypress.Commands.add('adminLogInUser', (credential) => {
    cy.get('#username').type(credential.login);
    cy.get('#login').type(credential.password);
    cy.get('.form-button').click();
})

Cypress.Commands.add('adminLogInValidUser', () => {
    cy.get('#username').type(credentials.Admin.login);
    cy.get('#login').type(credentials.Admin.password);
    cy.get('.form-button').click();
})

Cypress.Commands.add('adminLogInInvalidUser', () => {
    cy.get('#username').type(credentials.NoAdmin.login);
    cy.get('#login').type(credentials.NoAdmin.password);
    cy.get('.form-button').click();
})

Cypress.Commands.add('adminTestRoute', (route) => {
    cy.get('body').then($body => {
        if ($body.find('#message-popup-window .message-popup-head a').length > 0) {
            cy.get('#message-popup-window .message-popup-head a').click({force: true});
        }
    });
    cy.get(route.id).click({force: true});
    cy.url().should('include', route.url);
    cy.get('h3.icon-head').should('include.text', route.h3);
    //cy.get(route.parent).should('have.class', 'active');
})
Cypress.Commands.add('adminTestRouteH3', (nav, route) => {
    cy.get(route.id).click({force: true});
    cy.get('h3.icon-head').should('include.text', route.h3);
})

Cypress.Commands.add('adminTestRouteMainMenuActive', (nav, route) => {
    cy.get(route.id).click({force: true});
    cy.get(nav.main.id).should('have.class', 'active');
})