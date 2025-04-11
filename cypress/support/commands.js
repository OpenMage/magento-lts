Cypress.Commands.add('adminLogIn', () => {
    const username = cy.openmage.login.admin.username.value;
    const password = cy.openmage.login.admin.password.value;

    cy.visit('/admin');

    cy.log(`Logging in as ${username}`);
    cy.get('#username').clear().type(username).should('have.value', username);
    cy.get('#login').clear().type(password).should('have.value', password);
    cy.get('.form-button').click();

    cy.log('Checking for successful login');
    cy.url().should('include', '/dashboard/index');
})

Cypress.Commands.add('adminGoToTestRoute', (route) => {
    cy.get('body').then($body => {
        const popup = '#message-popup-window .message-popup-head a';
        if ($body.find(popup).length > 0) {
            cy.log('Dismissing popup');
            cy.get(popup).click({force: true});
        }
    });

    cy.log(`Clicking on "${route.h3}" menu`);
    cy.get(route._id).click({force: true});
    cy.url().should('include', route.url);
})

Cypress.Commands.add('adminTestRoute', (route) => {
    cy.log('Checking for title');
    cy.get(route._h3).should('include.text', route.h3);

    cy.log('Checking for active parent class');
    cy.get(route._id_parent).should('have.class', 'active');

    cy.log('Checking for active class');
    cy.get(route._id).should('have.class', 'active');
})

Cypress.Commands.add('adminGetConfiguration', (route) => {
    cy.get('#nav-admin-system-config').click({force: true});
    cy.url().should('include', 'system_config/index');

    cy.get(route._id).click({force: true});
    cy.url().should('include', route.url);
    cy.get('.content-header h3').should('include.text', route.h3);
})

Cypress.Commands.add('adminSaveConfiguration', () => {
    cy.log('Clicking on Save Config button');
    cy.get('.form-buttons button[title="Save Config"]').click({force: true, multiple: true});
})


