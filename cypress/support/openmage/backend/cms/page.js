const defaultConfig = {
    _button: '.form-buttons button',
}

cy.testBackendCmsPage = {
    config: {
        _id: '#nav-admin-cms-page',
        _id_parent: '#nav-admin-cms',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Manage Pages',
            url: 'cms_page/index',
            _grid: '#cmsPageGrid',
            __buttons: {
                add: defaultConfig._button + '[title="Add New Page"]',
            },
        },
        edit: {
            title: 'Edit Page',
            url: 'cms_page/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save Page"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                delete: defaultConfig._button + '[title="Delete Page"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
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
        },
        new: {
            title: 'New Page',
            url: 'cms_page/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save Page"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
        },
    },
}
