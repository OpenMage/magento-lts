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
