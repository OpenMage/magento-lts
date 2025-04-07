const navMenuItem = {
    cms: '#nav-admin-cms',
}

const routes = {
    Page: {
        parent: navMenuItem.cms,
        id: '#nav-admin-cms-page',
        url: '/admin/cms_page/index',
        h3: 'Manage Pages',
    },
    Block: {
        parent: navMenuItem.cms,
        id: '#nav-admin-cms-block',
        url: '/admin/cms_block/index',
        h3: 'Static Blocks',
    },
    WidgetInstance: {
        parent: navMenuItem.cms,
        id: '#nav-admin-cms-widget_instance',
        url: '/admin/widget_instance/index',
        h3: 'Manage Widget Instances',
    }
};

describe('Checks admin cms routes', () => {
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