const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.shipment;

/**
 * Configuration for "Shipments" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, view: {}}}
 */
test.config = {
    _: '#nav-admin-sales-shipment',
    _nav: '#nav-admin-sales',
    _title: base._title,
    _button: base._button,
    url: 'sales_shipment/index',
    index: {},
    view: {},
};

/**
 * Configuration for "Shipments" page
 * @type {{title: string, url: string, _grid: string}}
 */
test.config.index = {
    title: 'Shipments',
    url: test.config.url,
    _grid: '#sales_shipment_grid_table',
}

/**
 * Configuration for "View Shipment" page
 * @type {{__buttons: {print: string, back: string, tracking: string}, title: string, url: string}}
 */
test.config.view = {
    title: 'Shipment #',
    url: 'sales_shipment/view',
    __buttons: {
        print: {
            _: base._button + '[title="Print"]',
            __class: ['scalable', 'save', 'print'],
        },
        tracking: {
            _: base._button + '[title="Send Tracking Information"]',
            __class: ['scalable', 'send-email'],
        },
        back: base.__buttons.back,
    },
}
