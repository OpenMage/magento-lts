const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.account;
const tools = cy.openmage.tools;

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
 * @type {{__buttons: {save: string, reset: string}, title: string, __fields: {firstname: {selector: string}, email: {selector: string}, username: {selector: string}, lastname: {selector: string}, current_password: {selector: string}}, url: string}}
 */
test.config.index = {
    title: 'My Account',
    url: test.config.url,
    __buttons: {
        save: {
            _: base._button + '[title="Save Account"]',
            __class: base.__buttons.save.__class,
        },
        reset: {
            _: base.__buttons.reset._,
            __class: base.__buttons.reset.__class,
        },
    },
    clickSave: () => {
        tools.click(test.config.index.__buttons.save._, 'Save button clicked');
    },
    clickReset: () => {
        base.__buttons.reset.click();
    },
    __fields: {
        username: {
            _: '#username',
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
        },
    }
}
