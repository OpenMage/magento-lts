const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
};

base.__fields = {
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

base.__tabs = {
    general: '#page_tabs_main_section',
    content: '#page_tabs_content_section',
    design: '#page_tabs_design_section',
    metaData: '#page_tabs_meta_section',
}

cy.testBackendCmsPage = {};

cy.testBackendCmsPage.config = {
    _id: '#nav-admin-cms-page',
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
    _button: base._button,
    clickTabMain: () => {
        tools.click(base.__tabs.general, 'Clicking on General tab');
    },
    clickTabContent: () => {
        tools.click(base.__tabs.content, 'Clicking on Content tab');
    },
    clickTabDesign: () => {
        tools.click(base.__tabs.design, 'Clicking on Design tab');
    },
    clickTabMetaData: () => {
        tools.click(base.__tabs.metaData, 'Clicking on Meta Data tab');
    },
}

cy.testBackendCmsPage.config.index = {
    title: 'Manage Pages',
    url: 'cms_page/index',
    _grid: '#cmsPageGrid',
    __buttons: {
        add: base._button + '[title="Add New Page"]',
    },
    clickAdd: (log = 'Add New Page button clicked') => {
        tools.click(cy.testBackendCmsPage.config.index.__buttons.add, log);
    },
    clickGridRow: (selector = 'td', content = '', log = 'Select a CMS page') => {
        tools.clickContains(cy.testBackendCmsPage.config.index._grid, selector, content, log);
    },
}

cy.testBackendCmsPage.config.edit = {
    title: 'Edit Page',
    url: 'cms_page/edit',
    __buttons: {
        save: base._button + '[title="Save Page"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete Page"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    __tabs: base.__tabs,
    disablePage: () => {
        cy.log('Disable the CMS page');
        cy.get(base.__fields.page_is_active.selector)
            .select('Disabled');
    },
    resetStores: () => {
        cy.log('Restore the default store to the CMS page');
        cy.get(base.__fields.page_store_id.selector)
            .select([1, 2, 3]);
    },
    clickSave: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.saveAndContinue, 'Save and Continue Edit button clicked');
    },
    clickDelete: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.delete, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.reset, 'Reset button clicked');
    },
}

cy.testBackendCmsPage.config.new = {
    title: 'New Page',
    url: 'cms_page/new',
    __buttons: {
        save: base._button + '[title="Save Page"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    __tabs: base.__tabs,
    clickSave: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.saveAndContinue, 'Save and Continue Edit button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.reset, 'Reset button clicked');
    },
}
