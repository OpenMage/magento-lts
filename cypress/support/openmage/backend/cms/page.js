const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.cms.page;
const tools = cy.openmage.tools;

/**
 * Selectors for CMS page fields
 * @type {{page_identifier: {selector: string}, page_title: {selector: string}, page_meta_description: {selector: string}, page_root_template: {selector: string}, page_content_heading: {selector: string}, page_custom_root_template: {selector: string}, page_is_active: {selector: string}, page_meta_keywords: {selector: string}, page_store_id: {selector: string}, page_layout_update_xml: {selector: string}, page_custom_layout_update_xml: {selector: string}, page_content: {selector: string}, page_custom_theme: {selector: string}, page_custom_theme_from: {selector: string}, page_custom_theme_to: {selector: string}}}
 * @private
 */
test.__fields = {
    page_title : {
        selector: '#page_title',
    },
    page_identifier : {
        selector: '#page_identifier',
    },
    page_store_id : {
        selector: '#page_store_id',
    },
    page_is_active : {
        selector: '#page_is_active',
    },
    page_content_heading : {
        selector: '#page_content_heading',
    },
    page_content : {
        selector: '#page_content',
    },
    page_root_template : {
        selector: '#page_root_template',
    },
    page_layout_update_xml : {
        selector: '#page_layout_update_xml',
    },
    page_custom_theme_from : {
        selector: '#page_custom_theme_from',
    },
    page_custom_theme_to : {
        selector: '#page_custom_theme_to',
    },
    page_custom_theme : {
        selector: '#page_custom_theme',
    },
    page_custom_root_template : {
        selector: '#page_custom_root_template',
    },
    page_custom_layout_update_xml : {
        selector: '#page_custom_layout_update_xml',
    },
    page_meta_keywords : {
        selector: '#page_meta_keywords',
    },
    page_meta_description : {
        selector: '#page_meta_description',
    },
};

/**
 * Selectors for CMS page tabs
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
 * @type {{clickTabContent: cy.openmage.test.backend.cms.page.config.clickTabContent, _button: string, clickTabDesign: cy.openmage.test.backend.cms.page.config.clickTabDesign, _title: string, _id: string, clickTabMain: cy.openmage.test.backend.cms.page.config.clickTabMain, clickTabMetaData: cy.openmage.test.backend.cms.page.config.clickTabMetaData, _id_parent: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-cms-page',
    _id_parent: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    url: 'cms_page/index',
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
 * @type {{__buttons: {add: string}, clickGridRow: cy.openmage.test.backend.cms.page.config.index.clickGridRow, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.cms.page.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Manage Pages',
    url: test.config.url,
    _grid: '#cmsPageGrid',
    __buttons: {
        add: base._button + '[title="Add New Page"]',
    },
    clickAdd: (log = 'Add New Page button clicked') => {
        tools.click(test.config.index.__buttons.add, log);
    },
    clickGridRow: (content = '', selector = 'td') => {
        tools.grid.clickContains(test.config.index, content, selector);
    },
}

/**
 * Configuration for "Edit Page" page
 * @type {{resetStores: cy.openmage.test.backend.cms.page.config.edit.resetStores, __tabs: {general: string, metaData: string, design: string, content: string}, clickReset: cy.openmage.test.backend.cms.page.config.edit.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string, delete: string}, clickBack: cy.openmage.test.backend.cms.page.config.edit.clickBack, clickSave: cy.openmage.test.backend.cms.page.config.edit.clickSave, clickDelete: cy.openmage.test.backend.cms.page.config.edit.clickDelete, title: string, __fields, clickSaveAndContinue: cy.openmage.test.backend.cms.page.config.edit.clickSaveAndContinue, url: string, disablePage: cy.openmage.test.backend.cms.page.config.edit.disablePage}}
 */
test.config.edit = {
    title: 'Edit Page',
    url: 'cms_page/edit',
    __buttons: {
        save: base._button + '[title="Save Page"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete Page"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    __tabs: test.__tabs,
    disablePage: () => {
        cy.log('Disable the CMS page');
        cy.get(test.__fields.page_is_active.selector)
            .select('Disabled');
    },
    resetStores: () => {
        cy.log('Restore the default store to the CMS page');
        cy.get(test.__fields.page_store_id.selector)
            .select([1, 2, 3]);
    },
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(test.config.edit.__buttons.saveAndContinue, 'Save and Continue Edit button clicked');
    },
    clickDelete: () => {
        tools.click(test.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(test.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.edit.__buttons.reset, 'Reset button clicked');
    },
}

/**
 * Configuration for "New Page" page
 * @type {{__tabs: {general: string, metaData: string, design: string, content: string}, clickReset: cy.openmage.test.backend.cms.page.config.new.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string}, clickBack: cy.openmage.test.backend.cms.page.config.new.clickBack, clickSave: cy.openmage.test.backend.cms.page.config.new.clickSave, title: string, __fields, clickSaveAndContinue: cy.openmage.test.backend.cms.page.config.new.clickSaveAndContinue, url: string}}
 */
test.config.new = {
    title: 'New Page',
    url: 'cms_page/new',
    __buttons: {
        save: base._button + '[title="Save Page"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    __tabs: test.__tabs,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(test.config.new.__buttons.saveAndContinue, 'Save and Continue Edit button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset, 'Reset button clicked');
    },
}
