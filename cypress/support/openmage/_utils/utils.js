/**
 * Utility functions for OpenMage tests.
 * @type {{generateRandomEmail: (function(string=, string=): *), screenshot: cy.openmage.utils.screenshot}}
 */
cy.openmage.utils = {
    generateRandomEmail: (suffix = '-cypress-test', domain = '@example.com') => {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let email = '';
        for (let i = 0; i < 16; i++) {
            email += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return email + suffix + domain;
    },
    screenshot: (element, filename) => {
        cy.log('Taking screenshot');
        cy.get(element).screenshot(filename, { overwrite: true, padding: 10 });
    }
}
