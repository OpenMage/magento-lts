const navMenuItem = {
    sales: '#nav-admin-sales',
}

const routes = {
    Creditmemo: {
        parent: navMenuItem.sales,
        id: '#nav-admin-sales-creditmemo',
        url: '/admin/sales_creditmemo/index',
        h3: 'Credit Memos',
    },
    Invoice: {
        parent: navMenuItem.sales,
        id: '#nav-admin-sales-invoice',
        url: '/admin/sales_invoice/index',
        h3: 'Invoice',
    },
    Order: {
        parent: navMenuItem.sales,
        id: '#nav-admin-sales-order',
        url: '/admin/sales_order/index',
        h3: 'Orders',
    },
    Shipment: {
        parent: navMenuItem.sales,
        id: '#nav-admin-sales-shipment',
        url: '/admin/sales_shipment/index',
        h3: 'Shipments',
    },
    Transactions: {
        parent: navMenuItem.sales,
        id: '#nav-admin-sales-transactions',
        url: '/admin/sales_transactions/index',
        h3: 'Transactions',
    }
};

describe('Checks admin sales routes', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
    });

    Object.keys(routes).forEach(routeKey => {
        const route = routes[routeKey];
        it(`tests ${routeKey}`, () => {
            cy.adminTestRoute(route);
        });
    });
});