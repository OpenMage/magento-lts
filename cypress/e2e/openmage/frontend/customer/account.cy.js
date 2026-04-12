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
    it('Submits invalid form with weak password', () => {
        const password = '12345678';
        // see PR: https://github.com/OpenMage/magento-lts/pull/4617
        // const message = 'Thank you for registering with Madison Island.';
        const message = 'Password must include both numeric and alphabetic characters.';
        cy.get(test.create.__fields.firstname._).type(firstname).should('have.value', firstname);
        cy.get(test.create.__fields.lastname._).type(lastname).should('have.value', lastname);
        cy.get(test.create.__fields.email_address._).type(email).should('have.value', email);
        cy.get(test.create.__fields.password._).type(password).should('have.value', password);
        cy.get(test.create.__fields.confirmation._).type(password).should('have.value', password);
        tools.click(test._buttonSubmit);
        cy.screenshot();
        validation.hasErrorMessage(message);
    });

    it('Submits valid form', () => {
        const password = 'veryl0ngpassw0rd';
        let message = '';
        let filename = '';

        // see PR: https://github.com/OpenMage/magento-lts/pull/4617
        // const message = 'Thank you for registering with Madison Island.';
        message = 'Thank you for registering with ENV name default.';
        cy.get(test.create.__fields.firstname._).type(firstname).should('have.value', firstname);
        cy.get(test.create.__fields.lastname._).type(lastname).should('have.value', lastname);
        cy.get(test.create.__fields.email_address._).type(email).should('have.value', email);
        cy.get(test.create.__fields.password._).type(password).should('have.value', password);
        cy.get(test.create.__fields.confirmation._).type(password).should('have.value', password);
        tools.click(test._buttonSubmit);
        cy.screenshot();
        validation.hasSuccessMessage(message);
        
        const linkAccountInformation = 'div.block-account li:nth-child(2) a'; // todo: replace with selector for "Account Dashboard" link
        message = 'The account information has been saved.';

        cy.log('Check that account information can be changed with password');
        cy.get(linkAccountInformation).click();
        cy.get(test.edit.__fields.current_password._).type(password).should('have.value', password);
        tools.click(test._buttonSubmit);
        validation.hasSuccessMessage(message);

        cy.log('Check that password is required when changing account information');
        cy.get(linkAccountInformation).click();
        cy.get(test.edit.__fields.current_password._).type(password).should('have.value', password);
        cy.get('[name="change_password"]').check();
        validation.removeClasses(test.edit);
        tools.click(test._buttonSubmit);
        validation.hasErrorMessage();
    });
});
