const defaultConfig = {
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
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
        block_is_active : {
            selector: '#block_is_active',
        },
    },
}

cy.testBackendCmsBlock = {
    config: {
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
            url: 'cms_block/edit',
            __fields: defaultConfig.__fields,
        },
        new: {
            title: 'New Block',
            url: 'cms_block/new',
            __fields: defaultConfig.__fields,
        },
    },
}
