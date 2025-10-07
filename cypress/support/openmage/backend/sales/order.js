const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.order;

/**
 * Configuration for "Orders" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-sales-order',
    _id_parent: '#nav-admin-sales',
    _title: base._title,
    _button: base._button,
    url: 'sales_order/index',
};

/**
 * Configuration for "Orders" page
 * @type {{__buttons: {new: string}, title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Orders',
    url: test.config.url,
    _grid: '#sales_order_grid_table',
    __buttons: {
        new: '.form-buttons button[title="Create New Order"]',
    },
}

/**
 * Configuration for "View Order" page
 * @type {{__buttons: {back: string, reorder: string}, title: string, url: string}}
 */
test.config.view = {
    title: 'Order #',
    url: 'sales_order/view',
    __buttons: {
        reorder: base._button + '[title="Reorder"]',
        back: base._button + '[title="Back"]',
    },
}
