/**
 * Namespace for custom tools
 * @type {{click: (function(*, *=): void), grid: {clickFirstRow: (function(*): void), clickContains: (function(*, *, *=): void)}}}
 */
cy.openmage.tools = {
    click: (selector, log = 'Clicking on something') => {
        cy.log(log);
        cy.get(selector)
            .first()
            .click({ force: true, multiple: false });
    },
}

cy.openmage.tools.admin = {
    buttons: {
        clickAdd: (afterClickUrl) => {
            cy.log('Add button clicked');
            cy.getBySel('admin-button-add')
                .first()
                .click({ force: true, multiple: false });

            if (afterClickUrl !== undefined) {
                cy.url().should('include', afterClickUrl);
            }
        },
        clickBack: (afterClickUrl) => {
            cy.log('Back button clicked');
            cy.getBySel('admin-button-back')
                .first()
                .click({ force: true, multiple: false });

            if (afterClickUrl !== undefined) {
                cy.url().should('include', afterClickUrl);
            }
        },
        clickDelete: (afterClickUrl) => {
            cy.log('Delete button clicked');
            cy.getBySel('admin-button-delete')
                .first()
                .click({ force: true, multiple: false });

            if (afterClickUrl !== undefined) {
                cy.url().should('include', afterClickUrl);
            }
        },
        clickReset: (afterClickUrl) => {
            cy.log('Reset button clicked');
            cy.getBySel('admin-button-reset')
                .first()
                .click({ force: true, multiple: false });

            if (afterClickUrl !== undefined) {
                cy.url().should('include', afterClickUrl);
            }
        },
        clickSave: (afterClickUrl) => {
            cy.log('Save button clicked');
            cy.getBySel('admin-button-save')
                .first()
                .click({ force: true, multiple: false });

            if (afterClickUrl !== undefined) {
                cy.url().should('include', afterClickUrl);
            }
        },
        clickSaveAndContinue: (afterClickUrl) => {
            cy.log('Save and Continue button clicked');
            cy.getBySel('admin-button-save-and-continue')
                .first()
                .click({ force: true, multiple: false });

            if (afterClickUrl !== undefined) {
                cy.url().should('include', afterClickUrl);
            }
        }
    }
}

/**
 * Namespace for grid related tools
 * @type {{clickFirstRow: (function(*): void), clickContains: (function(*, *, *=): void)}}
 */
cy.openmage.tools.grid = {
    clickFirstRow: (path) => {
        cy.log('Clicking on first grid content');
        cy.get(path.grid._table + ' tbody')
            .find('td')
            .eq(2) // Click on the 3rd column, first can be a checkbox
            .should('be.visible')
            .click({ force: false, multiple: false });
    },
    clickSortedColumn: (path) => {
        cy.log('Clicking on sorted column header');
        cy.get(path.grid._table + ' thead th.sorted a')
            .first()
            .should('be.visible')
            .click({ force: false, multiple: false });
    },
    clickNotSortedColumn: (path) => {
        cy.log('Clicking on not sorted column header');
        cy.get(path.grid._table + ' thead th.not-sort a')
            .first()
            .should('be.visible')
            .click({ force: false, multiple: false });
    },
    clickContains: (path, content, _ = 'td') => {
        cy.log('Clicking on some grid content');
        cy.get(path.grid._table + ' tbody')
            .contains(_, content)
            .first()
            .should('be.visible')
            .click({ force: false, multiple: false });
    },
}