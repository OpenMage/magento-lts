const defaultConfig = {
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
}

cy.testBackendCmsPage = {
    config: {
        _id: '#nav-admin-cms-page',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Pages',
            url: 'cms_page/index',
            _grid: '#cmsPageGrid',
            __buttons: {
                add: '.form-buttons button[title="Add New Page"]',
            },
        },
        edit: {
            title: 'Edit Page',
            url: 'cms_page/edit',
            __buttons: {
                delete: '.form-buttons button[title="Delete Page"]',
                save: '.form-buttons button[title="Save Page"]',
                saveAndContinue: '.form-buttons button[title="Save and Continue Edit"]',
                reset: '.form-buttons button[title="Reset"]',
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
                save: '.form-buttons button[title="Save Page"]',
                saveAndContinue: '.form-buttons button[title="Save and Continue Edit"]',
                reset: '.form-buttons button[title="Reset"]',
            },
        },
    },
}
