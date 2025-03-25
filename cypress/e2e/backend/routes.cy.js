const routes = {
    cmsPage: {
        id: '#nav-admin-cms-page',
        url: '/admin/cms_page/index',
        h3: 'Manage Pages',
    },
    cmsBlock: {
        id: '#nav-admin-cms-block',
        url: '/admin/cms_block/index',
        h3: 'Static Blocks',
    },
    cmsWidgetInstance: {
        id: '#nav-admin-cms-widget_instance',
        url: '/admin/widget_instance/index',
        h3: 'Manage Widget Instances',
    },
    newsletterTemplates: {
        id: '#nav-admin-newsletter-template',
        url: '/admin/newsletter_template/index',
        h3: 'Newsletter Templates',
    },
    newsletterQueue: {
        id: '#nav-admin-newsletter-queue',
        url: '/admin/newsletter_queue/index',
        h3: 'Newsletter Queue',
    },
    newsletterSubscribers: {
        id: '#nav-admin-newsletter-subscriber',
        url: '/admin/newsletter_subscriber/index',
        h3: 'Newsletter Subscribers',
    },
    newsletterProblemReports: {
        id: '#nav-admin-newsletter-problem',
        url: '/admin/newsletter_problem/index',
        h3: 'Newsletter Problem Reports',
    },
    salesCreditmemo: {
        id: '#nav-admin-sales-creditmemo',
        url: '/admin/sales_creditmemo/index',
        h3: 'Credit Memos',
    },
    salesInvoice: {
        id: '#nav-admin-sales-invoice',
        url: '/admin/sales_invoice/index',
        h3: 'Invoice',
    },
    salesOrder: {
        id: '#nav-admin-sales-order',
        url: '/admin/sales_order/index',
        h3: 'Orders',
    },
    salesShipment: {
        id: '#nav-admin-sales-shipment',
        url: '/admin/sales_shipment/index',
        h3: 'Shipments',
    },
    salesTransactions: {
        id: '#nav-admin-sales-transactions',
        url: '/admin/sales_transactions/index',
        h3: 'Transactions',
    },
    salesShipment: {
        id: '#nav-admin-sales-shipment',
        url: '/admin/sales_shipment/index',
        h3: 'Shipments',
    },
    salesShipment: {
        id: '#nav-admin-sales-shipment',
        url: '/admin/sales_shipment/index',
        h3: 'Shipments',
    }
};

describe('Checks admin routes', () => {
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