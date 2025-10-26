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

/**
 * Namespace for grid related tools
 * @type {{clickFirstRow: (function(*): void), clickContains: (function(*, *, *=): void)}}
 */
cy.openmage.tools.grid = {
    clickFirstRow: (path) => {
        cy.log('Clicking on first grid content');
        cy.get(path._grid + ' tbody')
            .find('td')
            .eq(2) // Click on the 3rd column, first can be a checkbox
            .should('be.visible')
            .click({ force: false, multiple: false });
    },
    clickContains: (path, content, _ = 'td') => {
        cy.log('Clicking on some grid content');
        cy.get(path._grid + ' tbody')
            .contains(_, content)
            .first()
            .should('be.visible')
            .click({ force: false, multiple: false });
    },
}