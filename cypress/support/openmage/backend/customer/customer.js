const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.customer;
const tools = cy.openmage.tools;

/**
 * Configuration for "Manage Customers" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-customer-manage',
    _nav: '#nav-admin-customer',
    _title: base._title,
    _button: base._button,
    url: 'customer/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Manage Customers" page
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.customer.customer.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Manage Customers',
    url: test.config.url,
    _grid: '#customerGrid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Customer"]',
        },
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add._, 'Add New Customers button clicked');
    },
    clickGridRow: (content = '', _ = 'td') => {
        tools.grid.clickContains(test.config.index, content, _);
    },
}

/**
 * Configuration for "Edit Customer" page
 * @type {{__buttons: {createOrder: string, save: string, back: string, reset: string, saveAndContinue: string, delete: string}, title: string, url: string}}
 */
test.config.edit = {
    // comes from sample data
    // TODO: make dynamic, update template
    title: 'John Doe',
    url: 'customer/edit',
    __buttons: {
        save: {
            _: base._button + '[title="Save Customer"]',
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
        },
        delete: {
            _: base._button + '[title="Delete Customer"]',
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
        createOrder: {
            _: base._button + '[title="Create Order"]',
        },
    },
}

/**
 * Configuration for "New Customer" page
 * @type {{clickReset: cy.openmage.test.backend.customer.customer.config.new.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string}, clickBack: cy.openmage.test.backend.customer.customer.config.new.clickBack, clickSave: cy.openmage.test.backend.customer.customer.config.new.clickSave, title: string, __fields: {firstname: {_: string}, gender: {_: string}, prefix: {_: string}, middlename: {_: string}, suffix: {_: string}, lastname: {_: string}, password: {_: string}, group_id: {_: string}, sendemail_store_id: {_: string}, dob: {_: string}, sendemail: {_: string}, website_id: {_: string}, send_pass: {_: string}, email: {_: string}, taxvat: {_: string}}, clickSaveAndContinue: cy.openmage.test.backend.customer.customer.config.new.clickSaveAndContinue, url: string}}
 */
test.config.new = {
    title: 'New Customer',
    url: 'customer/new',
    __buttons: {
        save: {
            _: base._button + '[title="Save Customer"]',
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
    },
    __fields: {
        website_id : {
            _: '#_accountwebsite_id',
        },
        group_id : {
            _: '#_accountgroup_id',
        },
        prefix : {
            _: '#_accountprefix',
        },
        firstname : {
            _: '#_accountfirstname',
        },
        middlename : {
            _: '#_accountmiddlename',
        },
        lastname : {
            _: '#_accountlastname',
        },
        suffix : {
            _: '#_accountsuffix',
        },
        email : {
            _: '#_accountemail',
        },
        dob : {
            _: '#_accountdob',
        },
        taxvat : {
            _: '#_accounttaxvat',
        },
        gender : {
            _: '#_accountgender',
        },
        sendemail : {
            _: '#_accountsendemail',
        },
        sendemail_store_id : {
            _: '#_accountsendemail_store_id',
        },
        password : {
            _: '#_accountpassword',
        },
        send_pass: {
            _: '#account-send-pass',
        },
    },
    clickSave: () => {
        tools.click(test.config.new.__buttons.save._, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
    },
}
