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
    const username = credentials.Admin.login;
    const password = credentials.Admin.password;

    cy.log(`Logging in as ${username}`);
    cy.get('#username').clear().type(username).should('have.value', username);
    cy.get('#login').clear().type(password).should('have.value', password);
    cy.get('.form-button').click();

    cy.log('Checking for successful login');
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
            cy.log('Dismissing popup');
            cy.get('#message-popup-window .message-popup-head a').click({force: true});
        }
    });

    cy.log(`Clicking on "${route.h3}" menu`);
    cy.get(route.id).click({force: true});
    cy.url().should('include', route.url);

    cy.log('Checking for title');
    cy.get('h3.icon-head').should('include.text', route.h3);

    cy.log('Checking for active parent class');
    cy.get(route.parent).should('have.class', 'active');

    cy.log('Checking for active class');
    cy.get(route.id).should('have.class', 'active');
})
Cypress.Commands.add('adminGetConfiguration', (route) => {
    cy.get('#nav-admin-system-config').click({force: true});
    cy.url().should('include', 'system_config/index');

    cy.get(route.id).click({force: true});
    cy.url().should('include', route.url);
    cy.get('.content-header h3').should('include.text', route.h3);
})
Cypress.Commands.add('adminSaveConfiguration', () => {
    cy.log('Clicking on Save Config button');
    cy.get('.form-buttons button[title="Save Config"]').click({force: true, multiple: true});
})
Cypress.Commands.add('generateRandomEmail', () => {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let email = '';
    for (let i = 0; i < 16; i++) {
        email += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return email + '@example.com';
})
