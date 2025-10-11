/**
 * Namespace for checking various page elements
 * @type {{navigation: cy.openmage.check.navigation, buttons: cy.openmage.check.buttons, grid: cy.openmage.check.grid, tabs: cy.openmage.check.tabs, fields: cy.openmage.check.fields, title: cy.openmage.check.title, url: cy.openmage.check.url}}
 */
cy.openmage.check = {
    buttons: (config, path) => {
        cy.log('Checking for existing buttons');
        if (path.__buttons !== undefined) {
            cy.get(config._button).filter(':visible').should('have.length', Object.keys(path.__buttons).length);

            for (const button of Object.keys(path.__buttons)) {
                cy.get(path.__buttons[button]._).should('exist');
            };
        }
    },
    fields: (config, path) => {
        cy.log('Checking for existing fields');
        if (path.__fields !== undefined) {
            for (const field of Object.keys(path.__fields)) {
                cy.get(path.__fields[field]._).should('exist');
            };
        }
    },
    grid: (config, path) => {
        cy.log('Checking for existing grid');
        if (path._grid !== undefined) {
            cy.get(path._grid).should('exist');
        }
    },
    navigation: (config, path) => {
        cy.log('Checking for active navigation');
        cy.get(config._).should('have.class', 'active');
        cy.get(config._nav).should('have.class', 'active');
    },
    url: (config, path) => {
        cy.log('Checking for URL')
        cy.url().should('include', path.url);
    },
    tabs: (config, path) => {
        cy.log('Checking for tabs');
        if (path.__tabs !== undefined) {
            for (const tab of Object.keys(path.__tabs)) {
                cy.get(path.__tabs[tab]).should('exist');
            };
        }
    },
    title: (config, path) => {
        cy.log('Checking for title')
        cy.get(config._title).should('include.text', path.title);
    },
}
