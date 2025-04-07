const navMenuItem = {
    system: '#nav-admin-system',
}

const routes = {
    MyAccout: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-myaccount',
        url: '/admin/system_account/index',
        h3: 'My Account',
    },
    Notification: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-adminnotification',
        url: '/admin/notification/index',
        h3: 'Messages Inbox',
    },
    Design: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-design',
        url: '/admin/system_design/index',
        h3: 'Design',
    },
    TransactionalEmails: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-email_template',
        url: '/admin/system_email_template/index',
        h3: 'Transactional Emails',
    },
    Variable: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-variable',
        url: '/admin/system_variable/index',
        h3: 'Custom Variables',
    },
    Cache: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-cache',
        url: '/admin/cache/index',
        h3: 'Cache Storage Management',
    },
    Index: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-index',
        url: '/admin/process/list',
        h3: 'Index Management',
    },
    Stores: {
        parent: navMenuItem.system,
        id: '#nav-admin-system-store',
        url: '/admin/system_store/index',
        h3: 'Manage Stores',
    },
};

describe('Checks admin system routes', () => {
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