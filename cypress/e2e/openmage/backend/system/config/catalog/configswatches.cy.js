const test = cy.openmage.test.backend.system.config.catalog.configswatches.config;
const validate = {
    dimension: {
        _input: {
            productDetail: {
                height: '#configswatches_product_detail_dimensions_height',
                width: '#configswatches_product_detail_dimensions_width',
            },
            productList: {
                height: '#configswatches_product_listing_dimensions_height',
                width: '#configswatches_product_listing_dimensions_width',
            },
            layeredNav: {
                height: '#configswatches_layered_nav_dimensions_height',
                width: '#configswatches_layered_nav_dimensions_width',
            },
        }
    }
}

describe(`Checks admin system "${test.section.title}" settings`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.section);
        cy.openmage.admin.goToSection(test.section);
    });

    it(`tests non-digit dimensions`, () => {
        const value = cy.openmage.validation.test.float;

        Object.keys(validate.dimension._input).forEach(section => {
            const group = validate.dimension._input[section];

            cy
                .get(group.height)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);

            cy
                .get(group.width)
                .clear({ force: true })
                .type(value, { force: true })
                .should('have.value', value);
        });

        cy.adminSaveConfiguration();

        cy.log('Checking for error messages');
        const error = cy.openmage.validation.digits.error;
        cy.get('#advice-validate-digits-configswatches_product_detail_dimensions_height').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_product_detail_dimensions_width').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_product_listing_dimensions_height').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_product_listing_dimensions_width').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_layered_nav_dimensions_height').should('include.text', error);
        cy.get('#advice-validate-digits-configswatches_layered_nav_dimensions_width').should('include.text', error);
    });

    it(`tests empty dimensions`, () => {
        Object.keys(validate.dimension._input).forEach(field => {
            const selector = validate.dimension._input[field];

            cy
                .get(selector.height)
                .clear({ force: true })
                .should('have.value', '');

            cy
                .get(selector.width)
                .clear({ force: true })
                .should('have.value', '');
        });

        cy.adminSaveConfiguration();

        cy.log('Checking for error messages');
        const error = cy.openmage.validation.requiredEntry.error;
        cy.get('#advice-required-entry-configswatches_product_detail_dimensions_height').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_product_detail_dimensions_width').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_product_listing_dimensions_height').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_product_listing_dimensions_width').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_layered_nav_dimensions_height').should('include.text', error);
        cy.get('#advice-required-entry-configswatches_layered_nav_dimensions_width').should('include.text', error);
    });
});