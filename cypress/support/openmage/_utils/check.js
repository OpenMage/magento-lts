/**
 * Namespace for checking various page elements
 * @type {{navigation: cy.openmage.check.navigation, buttons: cy.openmage.check.buttons, grid: cy.openmage.check.grid, tabs: cy.openmage.check.tabs, fields: cy.openmage.check.fields, title: cy.openmage.check.title, url: cy.openmage.check.url}}
 */
cy.openmage.check = {
    buttons: (config, path) => {
        cy.log('Checking for existing buttons');
        if (path.__buttons !== undefined) {
            // Check if number of visible buttons is equal to the number of configured buttons
            cy.get(config._button).filter(':visible').should('have.length', Object.keys(path.__buttons).length);

            for (const button of Object.keys(path.__buttons)) {
                // Check if button exists
                cy.get(path.__buttons[button]._).should('exist');

                if (path.__buttons[button].__class !== undefined) {
                    // Check if button has correct classes
                    cy.get(path.__buttons[button]._)
                        .then((el) => {
                            cy.openmage.check.shouldHaveClasses(el, path.__buttons[button].__class)
                        })
                }
            };
        }
    },
    fields: (config, path) => {
        cy.log('Checking for existing fields');
        if (path.__fields !== undefined) {
            for (const field of Object.keys(path.__fields)) {
                // Check if field exists
                cy.get(path.__fields[field]._).should('exist');
            };
        }
    },
    grid: (config, path) => {
        cy.log('Checking for existing grid');
        if (path._grid !== undefined) {
            // Check if grid exists
            cy.get(path._grid).should('exist');
        }
    },
    navigation: (config, path) => {
        cy.log('Checking for active navigation');
        // Check if navigation menu item is active
        cy.get(config._).should('have.class', 'active');
        cy.get(config._nav).should('have.class', 'active');
    },
    url: (config, path) => {
        cy.log('Checking for URL')
        // Check if URL is correct
        cy.url().should('include', path.url);
    },
    tabs: (config, path) => {
        cy.log('Checking for tabs');
        if (path.__tabs !== undefined) {
            for (const tab of Object.keys(path.__tabs)) {
                // Check if tab exists
                cy.get(path.__tabs[tab]).should('exist');
            };
        }
    },
    title: (config, path) => {
        cy.log('Checking for title')
        // Check if title is correct
        cy.get(config._title).should('include.text', path.title);
    },
    shouldHaveClasses(element, classList = []) {
        // Check if element has all classes from the list
        classList.forEach((c) => {
            expect(element).to.have.class(c)
        })
    },
}
