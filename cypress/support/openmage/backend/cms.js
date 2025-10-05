const defaultConfig = {
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
    block: {
        __fields: {
            block_title : {
                selector: '#block_title',
            },
            block_identifier : {
                selector: '#block_identifier',
            },
        },
    },
}

cy.testBackendCms = {
    block: {
        _id: '#nav-admin-cms-block',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Static Blocks',
            url: 'cms_block/index',
            _grid: '#cmsBlockGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Block"]',
            },
        },
        edit: {
            title: 'Edit Block',
            url: 'cms_block/new',
        },
        new: {
            title: 'New Block',
            url: 'cms_block/new',
        },
    },
    page: {
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
    widget: {
        _id: '#nav-admin-cms-widget_instance',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Widget Instances',
            url: 'widget_instance/index',
            _grid: '#widgetInstanceGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Widget Instance"]',
            },
        },
        edit: {
            title: 'Widget',
            url: 'widget_instance/new',
        },
        new: {
            title: 'New Widget Instance',
            url: 'widget_instance/new',
        },
    }
}
