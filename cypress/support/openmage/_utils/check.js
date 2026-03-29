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
        const grid = path.grid;

        if (grid === undefined) {
            cy.log('No grid configuration provided, skipping grid checks');
            return;
        }

        cy.log('Checking for existing grid');
        cy.get(grid._).should('exist');

        cy.log('Checking for existing grid table');
        cy.get(grid._table).should('exist');

        if (grid.sort.dir !== '') {
            cy.log('Checking for default grid sorting');
            cy.get(grid._ + ' th.sorted a').should('have.attr', 'class', 'sort-arrow-' + grid.sort.dir);
        }

        if (grid.sort.order !== '') {
            cy.log('Checking for default grid sorting column');
            cy.get(grid._ + ' th.sorted a').should('have.attr', 'name', grid.sort.order);
        }
    },
    gridSort: (config, path, dir, order = '') => {
        cy.log('Checking for grid sorting');
        if (order === '') {
            order = path.grid.sort.order;
        }

        cy.get(path.grid._ + ' th.sorted a').should('have.attr', 'name', order);

        expect(path.grid.sort.order).not.equal('');
        expect(path.grid.sort.dir).not.equal(dir);

        switch (dir) {
            case 'asc':
                cy.log('Checking for ascending grid sorting');
                cy.get(path.grid._ + ' th.sorted a').should('have.class', 'sort-arrow-asc');
                break;
            case 'desc':
                cy.log('Checking for descending grid sorting');
                cy.get(path.grid._ + ' th.sorted a').should('have.class', 'sort-arrow-desc');
                break;
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
