const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.sales.shipment;

/**
 * Configuration for "Shipments" menu item
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string, index: {}, view: {}}}
 */
test.config = {
    _id: '#nav-admin-sales-shipment',
    _id_parent: '#nav-admin-sales',
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
        },
        tracking: {
            _: base._button + '[title="Send Tracking Information"]',
        },
        back: {
            _: base.__buttons.back._,
        },
    },
}
