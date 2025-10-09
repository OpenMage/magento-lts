/**
 * Validation methods
 * @type {{removeClasses: cy.openmage.validation.removeClasses, pageElements: cy.openmage.validation.pageElements, _hasMessage: cy.openmage.validation._hasMessage, fillFields: cy.openmage.validation.fillFields, _warningMessage: string, hasWarningMessage: cy.openmage.validation.hasWarningMessage, removeClassesAll: cy.openmage.validation.removeClassesAll, _errorMessage: string, validateFields: cy.openmage.validation.validateFields, hasErrorMessage: cy.openmage.validation.hasErrorMessage, _successMessage: string, _messagesContainer: string, hasSuccessMessage: cy.openmage.validation.hasSuccessMessage}}
 */
cy.openmage.validation = {
    _messagesContainer: '#messages',
    _errorMessage: '.error-msg',
    _successMessage: '.success-msg',
    _warningMessage: '.warning-msg',
    fillFields: (path, validation, value = '') =>{
        cy.log('Filling fields with invalid values');
        Object.keys(path.__fields).forEach(field => {
            const selector = path.__fields[field].selector;
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
    removeClasses: (path, log = 'Removing validation classes from fields') =>{
        cy.log(log);
        Object.keys(path.__fields).forEach(field => {
            const selector = path.__fields[field].selector;
            cy
                .get(selector)
                .invoke('removeClass');
        });
    },
    removeClassesAll: () =>{
        cy.log('Removing validation classes from all fields');
        cy.get('.form-list .value').invoke('removeClass');
    },
    pageElements: (config, path) => {
        cy.openmage.check.buttons(config, path);
        cy.openmage.check.fields(config, path);
        cy.openmage.check.grid(config, path);
        cy.openmage.check.navigation(config, path);
        cy.openmage.check.tabs(config, path);
        cy.openmage.check.title(config, path);
        cy.openmage.check.url(config, path);
    },
    validateFields: (path, validation, match = 'include.text') =>{
        cy.log('Checking for fields');
        Object.keys(path.__fields).forEach(field => {
            const selector = validation._error + path.__fields[field].selector.replace(/^\#/, "");
            cy.get(selector).should(match, validation.error);
        });
    },
    hasErrorMessage: (
        message,
        options = {
            match: 'include.text',
            screenshot: false,
            filename: ''
        },
    ) =>{
        cy.log('Checking for error messages');
        cy.openmage.validation._hasMessage(cy.openmage.validation._errorMessage, message, options);
    },
    hasSuccessMessage: (
        message,
        options = {
            match: 'include.text',
            screenshot: false,
            filename: ''
        },
    ) =>{
        cy.log('Checking for success messages');
        cy.openmage.validation._hasMessage(cy.openmage.validation._successMessage, message, options);
    },
    hasWarningMessage: (
        message,
        options = {
            match: 'include.text',
            screenshot: false,
            filename: ''
        },
    ) =>{
        cy.log('Checking for warning messages');
        cy.openmage.validation._hasMessage(cy.openmage.validation._warningMessage, message, options);
    },
    _hasMessage: (
        element,
        message,
        options = {
            match: 'include.text',
            screenshot: false,
            filename: ''
        },
    ) =>{
        cy.log('Process message');

        if (!options.match) {
            options.match = 'include.text';
        }

        cy.get(element).should(options.match, message);

        if (options.screenshot === true && options.filename) {
            cy.openmage.utils.screenshot(cy.openmage.validation._messagesContainer, options.filename);
        }
    },
}

cy.openmage.validation.test = {
    float: '1.1',
    number: '1',
    numberGreater1: '666',
    string: 'string',
}

cy.openmage.validation.requiredEntry = {
    css: 'required-entry',
    error: 'This is a required field.',
    _error: '#advice-required-entry-',
}

cy.openmage.validation.digits = {
    css: 'validate-digits',
    error: 'Please use numbers only in this field. Please avoid spaces or other characters such as dots or commas.',
    _error: '#advice-validate-digits-',
}

cy.openmage.validation.number = {
    css: 'validate-number',
    error: 'Please enter a valid number in this field.',
    _error: '#advice-validate-number-',
}

cy.openmage.validation.numberRange = {
    css: 'validate-number-range',
    error: 'The value is not within the specified range.',
    _error: '#advice-validate-number-range-',
}
