/**
 * Namespace for custom tools
 * @type {{clickContains: cy.openmage.tools.clickContains, click: cy.openmage.tools.click}}
 */
cy.openmage.tools = {
    click: (selector) => {
        cy.log('Clicking on something');
        cy.get(selector).first().click({ force: true, multiple: false });
    },
    clickContains: (element, content, selector = 'td') => {
        cy.log('Clicking on some grid content');
        cy.get(element).contains(selector, content).first().should('be.visible').click({ force: false, multiple: false });
    },
}

/**
 * Namespace for grid related tools
 * @type {{clickFirstRow: cy.openmage.tools.grid.clickFirstRow}}
 */
cy.openmage.tools.grid = {
    clickFirstRow: (path) => {
        cy.log('Clicking on first grid content');
        cy.get(path._grid + ' tbody').find('td a').first().should('be.visible').click({ force: false, multiple: false });
    },
}