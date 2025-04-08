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
    cy.get('#username').clear().type(credential.login).should('have.value', credential.login);
    cy.get('#login').clear().type(credential.password).should('have.value', credential.password);
    cy.get('.form-button').click();
})

Cypress.Commands.add('adminLogInValidUser', () => {
    cy.get('#username').clear().type(credentials.Admin.login).should('have.value', credentials.Admin.login);
    cy.get('#login').clear().type(credentials.Admin.password).should('have.value', credentials.Admin.password);
    cy.get('.form-button').click();
    cy.url().should('include', '/dashboard/index');
})

Cypress.Commands.add('adminLogInInvalidUser', () => {
    cy.get('#username').clear().type(credentials.NoAdmin.login).should('have.value', credentials.NoAdmin.login);
    cy.get('#login').clear().type(credentials.NoAdmin.password).should('have.value', credentials.NoAdmin.password);
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
    cy.get(route.parent).should('have.class', 'active');
    cy.get(route.id).should('have.class', 'active');
})
Cypress.Commands.add('adminGetConfiguration', (route) => {
    cy.get('#nav-admin-system-config').click({force: true});
    cy.url().should('include', 'system_config/index');
    cy.get(route.id).click({force: true});
    cy.url().should('include', 'system_config/edit');
    cy.get('.content-header h3').should('include.text', route.h3);
})
