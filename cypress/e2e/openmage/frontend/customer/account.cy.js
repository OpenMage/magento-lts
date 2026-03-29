const test = cy.openmage.test.frontend.customer.account.config;
const tools = cy.openmage.tools;
const utils = cy.openmage.utils;
const validation = cy.openmage.validation;

describe(test.title, () => {
    beforeEach('Prepare', () => {
        cy.visit(test.create.url);
        cy.fixture(test.__fixture).as('fixture');

        cy.get(test._title).should('include.text', test.create.title);
    });

    const email = cy.openmage.utils.generateRandomEmail();

    it('tests save empty, no js', function () {
        validation.fixture.fillFields(this.fixture.validCustomer, true);
        validation.fixture.removeClasses(this.fixture.validCustomer);
        tools.click(test._buttonSubmit);

        cy.get(validation._errorMessage);
        validation.hasErrorMessage('"First Name" is a required value.')
        validation.hasErrorMessage('"First Name" length must be equal or greater than 1 characters.')
        validation.hasErrorMessage('"Last Name" is a required value.')
        validation.hasErrorMessage('"Last Name" length must be equal or greater than 1 characters.')
        validation.hasErrorMessage('"Email" is a required value.');
        validation.hasErrorMessage('"Email" is a required value.');
    });

    it('Submits form with short password and wrong confirmation', function () {
        let data = this.fixture.invalidWeakPasswordConfirm;
        data.email_address.value = email;

        validation.fixture.fillFields(data);
        tools.click(test._buttonSubmit);
        cy.get('#advice-validate-password-password').should('include.text', 'Please enter more characters or clean leading or trailing spaces.');
        cy.get('#advice-validate-cpassword-confirmation').should('include.text', 'Please make sure your passwords match.');
    });

    it('Submits empty form', function () {
        validation.fixture.fillFields(this.fixture.validCustomer, true);
        tools.click(test._buttonSubmit);
        validation.fixture.validateFields(this.fixture.validCustomer, validation.requiredEntry);
    });

    it('Submits invalid form with weak password', function () {
        let data = this.fixture.invalidWeakPassword;
        data.email_address.value = email;

        validation.fixture.fillFields(data);
        tools.click(test._buttonSubmit);
        validation.hasErrorMessage('Password must include both numeric and alphabetic characters.');
    });

    it('Submits valid form', function () {
        let data = this.fixture.validCustomer;
        data.email_address.value = email;

        validation.fixture.fillFields(data);
        tools.click(test._buttonSubmit);
        validation.hasSuccessMessage('Thank you for registering with');
    });
});
