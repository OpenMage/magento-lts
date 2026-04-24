const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.customer.group;
const tools = cy.openmage.tools;

/**
 * Configuration for "Customer Groups" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-customer-group',
    _nav: '#nav-admin-customer',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/customer/group',
    url: 'admin/customer_group',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Customer Groups" page
 * @type {{title: string, url: string, grid: {}, clickGridRow: (function(*=, *=): void)}}
 */
test.config.index = {
    title: 'Customer Groups',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'type', dir: 'asc' } }},
    clickGridRow: (content = '', selector = 'td') => {
        tools.grid.clickContains(test.config.index, content, selector);
    },
}

/**
 * Configuration for "Edit Customer Group" page
  * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.editNoContinue}}
 * TODO: there can be 3 buttons, when group is deletable
 */
test.config.edit = {
    title: 'Edit Customer Group',
    url: 'customer_group/edit',
    __buttons: base.__buttonsSets.editNoContinue,
}

/**
 * Configuration for "New Customer Group" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.newNoContinue}}
 */
test.config.new = {
    title: 'New Customer Group',
    url: 'customer_group/new',
    __buttons: base.__buttonsSets.newNoContinue,
}
