const navMenuItem = {
    customer: '#nav-admin-customer',
}

const routes = {
    //customer
    Manage: {
        parent: navMenuItem.customer,
        id: '#nav-admin-customer-manage',
        url: '/admin/customer/index',
        h3: 'Manage Customers',
    },
    Groups: {
        parent: navMenuItem.customer,
        id: '#nav-admin-customer-group',
        url: '/admin/customer_group/index',
        h3: 'Customer Groups',
    },
    Online: {
        parent: navMenuItem.customer,
        id: '#nav-admin-customer-online',
        url: '/admin/customer_online/index',
        h3: 'Online Customers',
    }
};

describe('Checks admin customer routes', () => {
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