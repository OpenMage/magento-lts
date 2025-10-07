cy.openmage = {};

cy.openmage.login = {
    admin: {
        username: {
            _id: '#username',
            value: 'admin',
        },
        password: {
            _id: '#login',
            value: 'veryl0ngpassw0rd',
        },
        _submit: {
            __selector: '.form-button',
        }
    }
}

cy.openmage.check = {
    buttons: (test, path, log = 'Checking for existing buttons') => {
        cy.log(log);
        if (path.__buttons !== undefined) {
            cy.get(test._button).filter(':visible').should('have.length', Object.keys(path.__buttons).length);

            for (const button of Object.keys(path.__buttons)) {
                cy.get(path.__buttons[button]).should('exist');
            };
        }
    },
    fields: (path, log = 'Checking for existing fields') => {
        cy.log(log);
        if (path.__fields !== undefined) {
            for (const field of Object.keys(path.__fields)) {
                cy.get(path.__fields[field].selector).should('exist');
            };
        }
    },
    grid: (path, log = 'Checking for existing grid') => {
        if (path._grid !== undefined) {
            cy.log(log);
            cy.get(path._grid).should('exist');
        }
    },
    navigation: (test, log = 'Checkinng for URL') => {
        cy.log('Checking for active class');
        cy.get(test._id).should('have.class', 'active');

        cy.log('Checking for active parent class');
        cy.get(test._id_parent).should('have.class', 'active');
    },
    url: (path, log = 'Checkinng for URL') => {
        cy.log(log)
        cy.url().should('include', path.url);
    },
    tabs: (path, log = 'Checkinng for tabs') => {
        cy.log(log);
        if (path.__tabs !== undefined) {
            for (const tab of Object.keys(path.__tabs)) {
                cy.get(path.__tabs[tab]).should('exist');
            };
        }
    },
    title: (test, path, log = 'Checkinng for title') => {
        cy.log(log)
        cy.get(test._h3).should('include.text', path.title);
    },
}

cy.openmage.tools = {
    click: (selector, log = 'Clicking on something') => {
        cy.log(log);
        cy.get(selector).first().click({force: true, multiple: false});
    },
    clickContains: (element, selector, content, log = 'Clicking on some content') => {
        cy.log(log);
        cy.get(element).contains(selector, content).first().click({force: true, multiple: false});
    },
}

cy.openmage.utils = {
    generateRandomEmail: () => {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let email = '';
        for (let i = 0; i < 16; i++) {
            email += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return email + '-cypress-test@example.com';
    },
}

cy.openmage.validation = {
    test: {
        float: '1.1',
        number: '1',
        numberGreater1: '666',
        string: 'string',
    },
    requiredEntry: {
        css: 'required-entry',
        error: 'This is a required field.',
        _error: '#advice-required-entry-',
    },
    digits: {
        css: 'validate-digits',
        error: 'Please use numbers only in this field. Please avoid spaces or other characters such as dots or commas.',
        _error: '#advice-validate-digits-',
    },
    number: {
        css: 'validate-number',
        error: 'Please enter a valid number in this field.',
        _error: '#advice-validate-number-',
    },
    numberRange: {
        css: 'validate-number-range',
        error: 'The value is not within the specified range.',
        _error: '#advice-validate-number-range-',
    },
    _errorMessage: '.error-msg',
    _successMessage: '.success-msg',
    _warningMessage: '.warning-msg',
    fillFields: (fields, validation, value = '') =>{
        cy.log('Filling fields with invalid values');
        Object.keys(fields).forEach(field => {
            const selector = fields[field];
            cy
                .get(selector)
                .clear({ force: true })
                .should('have.class', validation.css);

            if (value !== '') {
                cy
                    .get(selector)
                    .type(value, { force: true })
                    .should('have.value', value)
            }
        });
    },
    removeClasses: (fields) =>{
        cy.log('Removing classes from fields');
        Object.keys(fields).forEach(field => {
            const selector = fields[field];
            cy
                .get(selector)
                .invoke('removeClass');
        });
    },
    pageElements: (test, path) => {
        cy.openmage.check.buttons(test, path);
        cy.openmage.check.fields(path);
        cy.openmage.check.grid(test);
        cy.openmage.check.navigation(test);
        cy.openmage.check.tabs(path);
        cy.openmage.check.title(test, path);
        cy.openmage.check.url(path);
    },
    validateFields: (fields, validation) =>{
        cy.log('Checking for error messages');
        Object.keys(fields).forEach(field => {
            const selector = validation._error + fields[field].replace(/^\#/, "");
            cy.get(selector).should('include.text', validation.error);
        });
    },
    hasErrorMessage: (message) =>{
        cy.log('Checking for error messages');
        cy.get(cy.openmage.validation._errorMessage).should('include.text', message);
    },
    hasSuccessMessage: (message) =>{
        cy.log('Checking for success messages');
        cy.get(cy.openmage.validation._successMessage).should('include.text', message);
    },
    hasWarningMessage: (message) =>{
        cy.log('Checking for warning messages');
        cy.get(cy.openmage.validation._warningMessage).should('include.text', message);
    },
}