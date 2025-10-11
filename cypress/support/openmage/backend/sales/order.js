const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.order;

/**
 * Configuration for "Orders" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, view: {}}}
 */
test.config = {
    _: '#nav-admin-sales-order',
    _nav: '#nav-admin-sales',
    _title: base._title,
    _button: base._button,
    url: 'sales_order/index',
    index: {},
    view: {},
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
        add: {
            _: base._button + '[title="Create New Order"]',
            __class: base.__buttons.add.__class,
        },
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
        reorder: {
            _: base._button + '[title="Reorder"]',
            __class: ['go'],
        },
        back: base.__buttons.back,
    },
}
