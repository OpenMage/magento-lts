const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.cms.page;
const tools = cy.openmage.tools;

/**
 * _s for CMS page fields
 * @type {{page_identifier: {_: string}, page_title: {_: string}, page_meta_description: {_: string}, page_root_template: {_: string}, page_content_heading: {_: string}, page_custom_root_template: {_: string}, page_is_active: {_: string}, page_meta_keywords: {_: string}, page_store_id: {_: string}, page_layout_update_xml: {_: string}, page_custom_layout_update_xml: {_: string}, page_content: {_: string}, page_custom_theme: {_: string}, page_custom_theme_from: {_: string}, page_custom_theme_to: {_: string}}}
 * @private
 */
test.__fields = {
    page_title : {
        _: '#page_title',
    },
    page_identifier : {
        _: '#page_identifier',
    },
    page_store_id : {
        _: '#page_store_id',
    },
    page_is_active : {
        _: '#page_is_active',
    },
    page_content_heading : {
        _: '#page_content_heading',
    },
    page_content : {
        _: '#page_content',
    },
    page_root_template : {
        _: '#page_root_template',
    },
    page_layout_update_xml : {
        _: '#page_layout_update_xml',
    },
    page_custom_theme_from : {
        _: '#page_custom_theme_from',
    },
    page_custom_theme_to : {
        _: '#page_custom_theme_to',
    },
    page_custom_theme : {
        _: '#page_custom_theme',
    },
    page_custom_root_template : {
        _: '#page_custom_root_template',
    },
    page_custom_layout_update_xml : {
        _: '#page_custom_layout_update_xml',
    },
    page_meta_keywords : {
        _: '#page_meta_keywords',
    },
    page_meta_description : {
        _: '#page_meta_description',
    },
};

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
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-cms-page',
    _nav: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    url: 'cms_page/index',
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
 * @type {{title: string, url: string, _grid: string, __buttons: {}, clickGridRow: cy.openmage.test.backend.cms.page.config.index.clickGridRow}}
 */
test.config.index = {
    title: 'Manage Pages',
    url: test.config.url,
    _grid: '#cmsPageGrid',
    __buttons: {},
    clickGridRow: (content = '', _ = 'td') => {
        tools.grid.clickContains(test.config.index, content, _);
    },
}

/**
 * Configuration for buttons on "Pages" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.cms.page.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Page"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add New Page button clicked');
        },
    },
}

/**
 * Configuration for "Edit Page" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new, __fields: test.config.new.__fields, __tabs: test.config.new.__tabs,disablePage: (function(): void), resetStores: (function(): void)}}
 */
test.config.edit = {
    title: 'Edit Page',
    url: 'cms_page/edit',
    __buttons: base.__buttonsSets.edit,
    __fields: test.__fields,
    __tabs: test.__tabs,
    disablePage: () => {
        cy.log('Disable the CMS page');
        cy.get(test.__fields.page_is_active._)
            .select('Disabled');
    },
    resetStores: () => {
        cy.log('Restore the default store to the CMS page');
        cy.get(test.__fields.page_store_id._)
            .select([1, 2, 3]);
    },
}

/**
 * Configuration for "New Page" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new, __fields: test.config.new.__fields, __tabs: test.config.new.__tabs}}
 */
test.config.new = {
    title: 'New Page',
    url: 'cms_page/new',
    __buttons: base.__buttonsSets.new,
    __fields: test.__fields,
    __tabs: test.__tabs,
}
