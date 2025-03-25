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
    }
};

describe('Checks admin cms routes', () => {
    beforeEach('Log in the user', () => {
        cy.visit('/admin');
        cy.adminLogInValidUser();
    });

    it('tests cms pages', () => {
        cy.adminTestRoute(routes.cmsPage);
    });
    it('tests cms blocks', () => {
        cy.adminTestRoute(routes.cmsBlock);
    });
    it('tests cms widgets', () => {
        cy.adminTestRoute(routes.cmsWidgetInstance);
    });
});