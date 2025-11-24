const test = cy.openmage.test.backend.system.currency.config;
const tools = cy.openmage.tools;
const validation = cy.openmage.validation;

describe(`Checks admin system "${test.index.title}"`, () => {
    beforeEach('Log in the user', () => {
        cy.openmage.admin.login();
        cy.openmage.admin.goToPage(test, test.index);
    });

    it(`tests index route`, () => {
        validation.pageElements(test, test.index);
    });

    const warning = 'Invalid input data for USD => EUR rate';
    const success = 'All valid rates have been saved.';

    it(`tests empty currency`, () => {
        cy.get('body').then($body => {
            if ($body.find(test.index.__validation._input.from).length > 0) {
                cy.get(test.index.__validation._input.from).clear();
                tools.click(test.index.__buttons.save);
                validation.hasWarningMessage(warning);
                validation.hasSuccessMessage(success);
            }
        });
    });

    it(`tests string currency`, () => {
        cy.get('body').then($body => {
            if ($body.find(test.index.__validation._input.from).length > 0) {
                cy.get(test.index.__validation._input.from).clear().type('abc');
                tools.click(test.index.__buttons.save);
                validation.hasWarningMessage(warning);
                validation.hasSuccessMessage(success);
            }
        });
    });
});
