const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.cms.block;
const tools = cy.openmage.tools;

/**
 * Configuration for "Static Blocks" menu item
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-cms-block',
    _nav: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/cms/block',
    url: 'admin/cms_block',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Static Blocks" page
 * @type {{title: string, url: string, grid: {}, __buttons: {}}}
 */
test.config.index = {
    title: 'Static Blocks',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: {order: 'title', dir: 'asc' } }},
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
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.edit}}
 */
test.config.edit = {
    title: 'Edit Block',
    url: 'cms_block/edit',
    __buttons: base.__buttonsSets.edit,
}

/**
 * Configuration for "New Block" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new}}
 */
test.config.new = {
    title: 'New Block',
    url: 'cms_block/new',
    __buttons: base.__buttonsSets.new,
}
