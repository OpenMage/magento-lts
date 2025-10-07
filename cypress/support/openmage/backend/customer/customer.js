const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button'
}

cy.testBackendCustomerCustomer = {};

cy.testBackendCustomerCustomer.config = {
    _id: '#nav-admin-customer-manage',
    _id_parent: '#nav-admin-customer',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCustomerCustomer.config.index = {
    title: 'Manage Customers',
    url: 'customer/index',
    _grid: '#customerGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Customer"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendCustomerCustomer.config.index.__buttons.add, 'Add New Customers button clicked');
    },
}

cy.testBackendCustomerCustomer.config.edit = {
    title: 'John Smith', // comes from sample data
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

cy.testBackendCustomerCustomer.config.new = {
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
        group_id: {
            selector: '#_accountgroup_id',
        },
        send_pass: {
            selector: '#account-send-pass',
        },
    },
    clickSave: () => {
        tools.click(cy.testBackendCustomerCustomer.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendCustomerCustomer.config.new.__buttons.saveAndContinue, 'Save and Continue button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCustomerCustomer.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCustomerCustomer.config.new.__buttons.reset, 'Reset button clicked');
    },
}
