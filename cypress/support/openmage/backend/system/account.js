const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.account;
const admin = cy.openmage.admin;
const tools = cy.openmage.tools;

/**
 * Selectors for "My Account" page fields
 * @type {{firstname: {_: string}, email: {_: string}, username: {value: string, _: string}, lastname: {_: string}, current_password: {value: string, _: string}}}
 * @private
 */
test.__fields = {
    username: {
        _: '#username',
        value: admin.username.value,
    },
    firstname: {
        _: '#firstname',
    },
    lastname: {
        _: '#lastname',
    },
    email: {
        _: '#email',
    },
    current_password: {
        _: '#current_password',
        value: admin.password.value,
    },
}

/**
 * Configuration for "My Account" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-system-myaccount',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_account/index',
    index: {},
}

/**
 * Configuration for "My Account" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.newNoContinue, __fields: test.config.index.__fields}}
 */
test.config.index = {
    title: 'My Account',
    url: test.config.url,
    __buttons: base.__buttonsSets.newNoContinue,
    __fields: test.__fields,
}
