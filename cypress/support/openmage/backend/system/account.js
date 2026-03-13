const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.account;
const admin = cy.openmage.admin;
const tools = cy.openmage.tools;

/**
 * Configuration for "My Account" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-system-myaccount',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/system/account',
    url: 'admin/system_account',
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
