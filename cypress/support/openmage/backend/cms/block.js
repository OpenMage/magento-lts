const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.cms.block;
const tools = cy.openmage.tools;

/**
 * Configuration for CMS Block tests
 * @type {{block_title: {selector: string}, block_is_active: {selector: string}, block_content: {selector: string}, block_store_id: {selector: string}, block_identifier: {selector: string}}}
 * @private
 */
test.__fields = {
    block_title : {
        _: '#block_title',
    },
    block_identifier : {
        _: '#block_identifier',
    },
    block_store_id : {
        _: '#block_store_id',
    },
    block_is_active : {
        _: '#block_is_active',
    },
    block_content : {
        _: '#block_content',
    },
}

/**
 * Configuration for "Static Blocks" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-cms-block',
    _nav: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    url: 'cms_block/index',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Static Blocks" page
 * @type {{title: string, url: string, _grid: string, __buttons: {}}}
 */
test.config.index = {
    title: 'Static Blocks',
    url: test.config.url,
    _grid: '#cmsBlockGrid_table',
    __buttons: {},
}

/**
 * Configuration for buttons on "Static Blocks" page
 * @type {{add: {__class: string[], click: cy.openmage.test.backend.cms.block.config.index.__buttons.add.click, _: string}}}
 * @private
 */
test.config.index.__buttons = {
    add: {
        _: base._button + '[title="Add New Block"]',
        __class: base.__buttons.add.__class,
        click: () => {
            tools.click(test.config.index.__buttons.add._, 'Add CMS Blocks button clicked');
        },
    },
}

/**
 * Configuration for "Edit Block" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.edit, __fields: test.config.edit.__fields}}
 */
test.config.edit = {
    title: 'Edit Block',
    url: 'cms_block/edit',
    __buttons: base.__buttonsSets.edit,
    __fields: test.__fields,
}

/**
 * Configuration for "New Block" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new, __fields: test.config.new.__fields}}
 */
test.config.new = {
    title: 'New Block',
    url: 'cms_block/new',
    __buttons: base.__buttonsSets.new,
    __fields: test.__fields,
}
