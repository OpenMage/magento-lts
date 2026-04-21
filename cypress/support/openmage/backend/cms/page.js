const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.cms.page;
const tools = cy.openmage.tools;

/**
 * _s for CMS page tabs
 * @type {{general: string, metaData: string, design: string, content: string}}
 * @private
 */
test.__tabs = {
    general: '#page_tabs_main_section',
    content: '#page_tabs_content_section',
    design: '#page_tabs_design_section',
    metaData: '#page_tabs_meta_section',
}

/**
 * Configuration for "Pages" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string,  __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-cms-page',
    _nav: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/cms/page',
    url: 'admin/cms_page',
    index: {},
    edit: {},
    new: {},
    clickTabMain: () => {
        tools.click(test.__tabs.general, 'Clicking on General tab');
    },
    clickTabContent: () => {
        tools.click(test.__tabs.content, 'Clicking on Content tab');
    },
    clickTabDesign: () => {
        tools.click(test.__tabs.design, 'Clicking on Design tab');
    },
    clickTabMetaData: () => {
        tools.click(test.__tabs.metaData, 'Clicking on Meta Data tab');
    },
}

/**
 * Configuration for "Pages" page
 * @type {{title: string, url: string, grid: {}, clickGridRow: cy.openmage.test.backend.cms.page.config.index.clickGridRow}}
 */
test.config.index = {
    title: 'Manage Pages',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'title', dir: 'asc' } }},
    clickGridRow: (content = '', _ = 'td') => {
        tools.grid.clickContains(test.config.index, content, _);
    },
}

/**
 * Configuration for "Edit Page" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new, __tabs: test.config.new.__tabs,disablePage: (function(): void), resetStores: (function(): void)}}
 */
test.config.edit = {
    title: 'Edit Page',
    url: 'cms_page/edit',
    __buttons: base.__buttonsSets.edit,
    __tabs: test.__tabs,
    disablePage: () => {
        cy.log('Disable the CMS page');
        cy.getBySel('input-page-is-active')
            .select('Disabled');
    },
    resetStores: () => {
        cy.log('Restore the default store to the CMS page');
        cy.getBySel('input-page-store-id')
            .select([1, 2, 3]);
    },
}

/**
 * Configuration for "New Page" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new, __tabs: test.config.new.__tabs}}
 */
test.config.new = {
    title: 'New Page',
    url: 'cms_page/new',
    __buttons: base.__buttonsSets.new,
    __tabs: test.__tabs,
}
