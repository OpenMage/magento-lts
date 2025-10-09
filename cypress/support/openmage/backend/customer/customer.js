const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.customer;
const tools = cy.openmage.tools;

/**
 * Configuration for "Manage Customers" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-customer-manage',
    _id_parent: '#nav-admin-customer',
    _title: base._title,
    _button: base._button,
    url: 'customer/index',
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
        add: base._button + '[title="Add New Customer"]',
    },
    clickAdd: () => {
        tools.click(test.config.index.__buttons.add, 'Add New Customers button clicked');
    },
}

/**
 * Configuration for "Edit Customer" page
 * @type {{__buttons: {createOrder: string, save: string, back: string, reset: string, saveAndContinue: string, delete: string}, title: string, url: string}}
 */
test.config.edit = {
    // comes from sample data
    // TODO: make dynamic, update template
    title: 'John',
    url: 'customer/edit',
    __buttons: {
        save: base._button + '[title="Save Customer"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete Customer"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
        createOrder: base._button + '[title="Create Order"]',
    },
}

/**
 * Configuration for "New Customer" page
 * @type {{clickReset: cy.openmage.test.backend.customer.customer.config.new.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string}, clickBack: cy.openmage.test.backend.customer.customer.config.new.clickBack, clickSave: cy.openmage.test.backend.customer.customer.config.new.clickSave, title: string, __fields: {firstname: {selector: string}, gender: {selector: string}, prefix: {selector: string}, middlename: {selector: string}, suffix: {selector: string}, lastname: {selector: string}, password: {selector: string}, group_id: {selector: string}, sendemail_store_id: {selector: string}, dob: {selector: string}, sendemail: {selector: string}, website_id: {selector: string}, send_pass: {selector: string}, email: {selector: string}, taxvat: {selector: string}}, clickSaveAndContinue: cy.openmage.test.backend.customer.customer.config.new.clickSaveAndContinue, url: string}}
 */
test.config.new = {
    title: 'New Customer',
    url: 'customer/new',
    __buttons: {
        save: base._button + '[title="Save Customer"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: {
        website_id : {
            selector: '#_accountwebsite_id',
        },
        group_id : {
            selector: '#_accountgroup_id',
        },
        prefix : {
            selector: '#_accountprefix',
        },
        firstname : {
            selector: '#_accountfirstname',
        },
        middlename : {
            selector: '#_accountmiddlename',
        },
        lastname : {
            selector: '#_accountlastname',
        },
        suffix : {
            selector: '#_accountsuffix',
        },
        email : {
            selector: '#_accountemail',
        },
        dob : {
            selector: '#_accountdob',
        },
        taxvat : {
            selector: '#_accounttaxvat',
        },
        gender : {
            selector: '#_accountgender',
        },
        sendemail : {
            selector: '#_accountsendemail',
        },
        sendemail_store_id : {
            selector: '#_accountsendemail_store_id',
        },
        password : {
            selector: '#_accountpassword',
        },
        send_pass: {
            selector: '#account-send-pass',
        },
    },
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(test.config.new.__buttons.saveAndContinue, 'Save and Continue button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset, 'Reset button clicked');
    },
}
