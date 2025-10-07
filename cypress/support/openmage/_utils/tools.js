/**
 * Namespace for custom tools
 * @type {{clickContains: cy.openmage.tools.clickContains, click: cy.openmage.tools.click}}
 */
cy.openmage.tools = {
    click: (selector) => {
        cy.log('Clicking on something');
        cy.get(selector).first().should('be.visible').click({ force: false, multiple: false });
    },
    clickContains: (element, selector = 'td', content) => {
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
        cy.get(path._grid + ' tbody').find('td.sorted').first().should('be.visible').click({ force: false, multiple: false });
    },
}