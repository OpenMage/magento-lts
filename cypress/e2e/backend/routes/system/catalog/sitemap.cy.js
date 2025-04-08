const route = {
    id: '#section-sitemap',
    url: 'system_config/edit/section/sitemap',
    h3: 'Google Sitemap',
    validate: {
        priority: {
            invalidString: 'string',
            invalidNumber: '666',
            valid: '1',
        }
    }
}

describe('Checks admin system sitmap settings', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
        cy.adminGetConfiguration(route);
    });

    it(`tests invalid string priority`, () => {
        cy
            .get('#sitemap_category_priority')
            .clear({ force: true })
            .type(route.validate.priority.invalidString)
            .should('have.value', route.validate.priority.invalidString);
        cy
            .get('#sitemap_product_priority')
            .clear({ force: true })
            .type(route.validate.priority.invalidString)
            .should('have.value', route.validate.priority.invalidString);
        cy
            .get('#sitemap_page_priority')
            .clear({ force: true })
            .type(route.validate.priority.invalidString)
            .should('have.value', route.validate.priority.invalidString);

        cy.log('Clicking on Save Config button');
        cy.get('.form-buttons button[title="Save Config"]').click({force: true, multiple: true});

        console.log('Checking for error messages');
        cy.get('#advice-validate-number-sitemap_category_priority').should('include.text', 'Please enter a valid number in this field.');
        cy.get('#advice-validate-number-sitemap_product_priority').should('include.text', 'Please enter a valid number in this field.');
        cy.get('#advice-validate-number-sitemap_page_priority').should('include.text', 'Please enter a valid number in this field.');
    });

    it(`tests invalid number priority`, () => {
        cy
            .get('#sitemap_category_priority')
            .clear({ force: true })
            .type(route.validate.priority.invalidNumber)
            .should('have.value', route.validate.priority.invalidNumber);
        cy
            .get('#sitemap_product_priority')
            .clear({ force: true })
            .type(route.validate.priority.invalidNumber)
            .should('have.value', route.validate.priority.invalidNumber);
        cy
            .get('#sitemap_page_priority')
            .clear({ force: true })
            .type(route.validate.priority.invalidNumber)
            .should('have.value', route.validate.priority.invalidNumber);

        cy.log('Clicking on Save Config button');
        cy.get('.form-buttons button[title="Save Config"]').click({force: true, multiple: true});

        console.log('Checking for error messages');
        cy.get('#advice-validate-number-range-sitemap_category_priority').should('include.text', 'The value is not within the specified range.');
        cy.get('#advice-validate-number-range-sitemap_product_priority').should('include.text', 'The value is not within the specified range.');
        cy.get('#advice-validate-number-range-sitemap_page_priority').should('include.text', 'The value is not within the specified range.');
    });

    it(`tests empty priority`, () => {
        cy
            .get('#sitemap_category_priority')
            .clear({ force: true })
            .should('have.value', '');
        cy
            .get('#sitemap_product_priority')
            .clear({ force: true })
            .should('have.value', '');
        cy
            .get('#sitemap_page_priority')
            .clear({ force: true })
            .should('have.value', '');

        cy.log('Clicking on Save Config button');
        cy.get('.form-buttons button[title="Save Config"]').click({force: true, multiple: true});

        console.log('Checking for error messages');
        cy.get('#advice-required-entry-sitemap_category_priority').should('include.text', 'This is a required field.');
        cy.get('#advice-required-entry-sitemap_product_priority').should('include.text', 'This is a required field.');
        cy.get('#advice-required-entry-sitemap_page_priority').should('include.text', 'This is a required field.');
    });
});