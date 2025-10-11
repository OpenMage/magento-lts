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
 * @type {{_button: string, _title: string, _id: string, _id_parent: string, url: string, index: {}, edit: {}, new: {}, clickTabMain: cy.openmage.test.backend.cms.page.config.clickTabMain, clickTabContent: cy.openmage.test.backend.cms.page.config.clickTabContent, clickTabDesign: cy.openmage.test.backend.cms.page.config.clickTabDesign, clickTabMetaData: cy.openmage.test.backend.cms.page.config.clickTabMetaData}}
 */
test.config = {
    _id: '#nav-admin-cms-page',
    _id_parent: '#nav-admin-cms',
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
 * @type {{__buttons: {add: string}, clickGridRow: cy.openmage.test.backend.cms.page.config.index.clickGridRow, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.cms.page.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Manage Pages',
    url: test.config.url,
    _grid: '#cmsPageGrid',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Page"]',
        },
    },
    clickAdd: (log = 'Add New Page button clicked') => {
        tools.click(test.config.index.__buttons.add._, log);
    },
    clickGridRow: (content = '', _ = 'td') => {
        tools.grid.clickContains(test.config.index, content, _);
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
        save: {
            _: base._button + '[title="Save Page"]',
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
        },
        delete: {
            _: base._button + '[title="Delete Page"]',
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
    },
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
    clickSave: () => {
        tools.click(test.config.edit.__buttons.save._, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
    clickDelete: () => {
        tools.click(test.config.edit.__buttons.delete._, 'Delete button clicked');
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
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
        save: {
            _: base._button + '[title="Save Page"]'
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
        },
        back: {
            _: base.__buttons.back._,
        },
        reset: {
            _: base.__buttons.reset._,
        },
    },
    __fields: test.__fields,
    __tabs: test.__tabs,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        base.__buttons.saveAndContinue.click();
    },
    clickBack: () => {
        base.__buttons.back.click();
    },
    clickReset: () => {
        base.__buttons.reset.click();
    },
}
