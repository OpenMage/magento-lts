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
    tools: {
        generateRandomEmail: () => {
            const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            let email = '';
            for (let i = 0; i < 16; i++) {
                email += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return email + '-cypress-test@example.com';
        }
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
        saveAction: (selector) => {
            cy.log('Clicking on Save button');
            cy.get(selector).click({force: true, multiple: true});
        },
        validateFields: (fields, validation) =>{
            cy.log('Checking for error messages');
            Object.keys(fields).forEach(field => {
                const selector = validation._error + fields[field].replace(/^\#/, "");
                cy.get(selector).should('include.text', validation.error);
            });
        },
    }
}