const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

cy.testBackendCmsPage = {};

cy.testBackendCmsPage.config = {
    _id: '#nav-admin-cms-page',
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCmsPage.config.index = {
    title: 'Manage Pages',
    url: 'cms_page/index',
    _grid: '#cmsPageGrid',
    __buttons: {
        add: base._button + '[title="Add New Page"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendCmsPage.config.index.__buttons.add);
    },
    clickGridRow: (selector = 'td', content = 'no-route') => {
        tools.clickContains(cy.testBackendCmsPage.config.index._grid, selector, content, 'Select a CMS page');
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
    disablePage: () => {
        cy.log('Disable the CMS page');
        cy.get('#page_is_active')
            .select('Disabled');
    },
    resetStores: () => {
        cy.log('Restore the default store to the CMS page');
        cy.get('#page_store_id')
            .select([1, 2, 3]);
    },
    clickDelete: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.delete);
    },
    clickSave: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.save);
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.saveAndContinue);
    },
    clickBack: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.back);
    },
    clickReset: () => {
        tools.click(cy.testBackendCmsPage.config.edit.__buttons.reset);
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
    clickSave: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.save);
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.saveAndContinue);
    },
    clickBack: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.back);
    },
    clickReset: () => {
        tools.click(cy.testBackendCmsPage.config.new.__buttons.reset);
    },
}
