const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.account;

/**
 * Configuration for "My Account" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-system-myaccount',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    url: 'system_account/index',
}

/**
 * Configuration for "My Account" page
 * @type {{__buttons: {save: string, reset: string}, title: string, __fields: {firstname: {selector: string}, email: {selector: string}, username: {selector: string}, lastname: {selector: string}, current_password: {selector: string}}, url: string}}
 */
test.config.index = {
    title: 'My Account',
    url: test.config.url,
    __buttons: {
        save: base._button + '[title="Save Account"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: {
        username: {
            selector: '#username',
        },
        firstname: {
            selector: '#firstname',
        },
        lastname: {
            selector: '#lastname',
        },
        email: {
            selector: '#email',
        },
        current_password: {
            selector: '#current_password',
        },
    }
}
