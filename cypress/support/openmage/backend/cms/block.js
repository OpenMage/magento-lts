const defaultConfig = {
    _button: '.form-buttons button',
    __fields: {
        block_title : {
            selector: '#block_title',
        },
        block_identifier : {
            selector: '#block_identifier',
        },
        block_store_id : {
            selector: '#block_store_id',
        },
        block_is_active : {
            selector: '#block_is_active',
        },
        block_content : {
            selector: '#block_content',
        },
    },
}

cy.testBackendCmsBlock = {
    config: {
        _id: '#nav-admin-cms-block',
        _id_parent: '#nav-admin-cms',
        _h3: 'h3.icon-head',
        _button: defaultConfig._button,
        index: {
            title: 'Static Blocks',
            url: 'cms_block/index',
            _grid: '#cmsBlockGrid_table',
            __buttons: {
                add: defaultConfig._button + '[title="Add New Block"]',
            },
        },
        edit: {
            title: 'Edit Block',
            url: 'cms_block/edit',
            __buttons: {
                save: defaultConfig._button + '[title="Save Block"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                delete: defaultConfig._button + '[title="Delete Block"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
            __fields: defaultConfig.__fields,
        },
        new: {
            title: 'New Block',
            url: 'cms_block/new',
            __buttons: {
                save: defaultConfig._button + '[title="Save Block"]',
                saveAndContinue: defaultConfig._button + '[title="Save and Continue Edit"]',
                back: defaultConfig._button + '[title="Back"]',
                reset: defaultConfig._button + '[title="Reset"]',
            },
            __fields: defaultConfig.__fields,
        },
    },
}
