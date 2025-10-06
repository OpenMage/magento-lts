cy.openmage = {
    login: {
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
    },
    check: {
        buttons: (path, log = 'Checking for existing buttons') => {
            cy.log(log);
            if (path.__buttons !== undefined) {
                Object.keys(path.__buttons).forEach(button => {
                    cy.get(path.__buttons[button]).should('exist');
                });
            }
        },
        fields: (path, log = 'Checking for existing fields') => {
            cy.log(log);
            if (path.__fields !== undefined) {
                Object.keys(path.__fields).forEach(field => {
                    cy.get(path.__fields[field]).should('exist');
                });
            }
        },
        pageElements: (test, path) => {
            cy.log('Checking for title');
            cy.get(test._h3).should('include.text', path.title);

            cy.log('Checking for active parent class');
            cy.get(test._id_parent).should('have.class', 'active');

            cy.log('Checking for active class');
            cy.get(test._id).should('have.class', 'active');

            if (path._grid !== undefined) {
                cy.log('Checking for existing grid');
                cy.get(path._grid).should('exist');
            }

            if (path.__buttons !== undefined) {
                cy.log('Checking for existing buttons');
                Object.keys(path.__buttons).forEach(button => {
                    cy.get(path.__buttons[button]).should('exist');
                });
            }

            if (path.__fields !== undefined) {
                cy.log('Checking for existing fields');
                Object.keys(path.__fields).forEach(field => {
                    cy.get(path.__fields[field].selector).should('exist');
                });
            }
        },
    },
    tools: {
        clickAction: (selector, log = 'Clicking on button') => {
            cy.log(log);
            cy.get(selector).first().click({force: true, multiple: false});
        },
        clickGridRow: (grid, selector, content, log = 'Clicking on grid') => {
            cy.log(log);
            cy.get(grid).contains(selector, content).first().click({force: true, multiple: false});
        },
        generateRandomEmail: () => {
            const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            let email = '';
            for (let i = 0; i < 16; i++) {
                email += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return email + '-cypress-test@example.com';
        },
    },
    validation: {
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
        validateFields: (fields, validation) =>{
            cy.log('Checking for error messages');
            Object.keys(fields).forEach(field => {
                const selector = validation._error + fields[field].replace(/^\#/, "");
                cy.get(selector).should('include.text', validation.error);
            });
        },
        hasErrorMessage: (message) =>{
            cy.log('Checking for error messages');
            cy.get('.error-msg').should('include.text', message);
        },
        hasSuccessMessage: (message) =>{
            cy.log('Checking for success messages');
            cy.get('.success-msg').should('include.text', message);
        },
        hasWarningMessage: (message) =>{
            cy.log('Checking for warning messages');
            cy.get('.warning-msg').should('include.text', message);
        },
    }
}