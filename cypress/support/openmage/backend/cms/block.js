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
 * @type {{__buttons: {add: string}, title: string, url: string, _grid: string, clickAdd: cy.openmage.test.backend.cms.block.config.index.clickAdd}}
 */
test.config.index = {
    title: 'Static Blocks',
    url: test.config.url,
    _grid: '#cmsBlockGrid_table',
    __buttons: {
        add: {
            _: base._button + '[title="Add New Block"]',
            __class: ['scalable', 'add'],
        },
    },
    clickAdd: (log = 'Add CMS Blocks button clicked') => {
        tools.click(test.config.index.__buttons.add._, log);
    },
}

/**
 * Configuration for "Edit Block" page
 * @type {{clickReset: cy.openmage.test.backend.cms.block.config.edit.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string, delete: string}, clickBack: cy.openmage.test.backend.cms.block.config.edit.clickBack, clickSave: cy.openmage.test.backend.cms.block.config.edit.clickSave, clickDelete: cy.openmage.test.backend.cms.block.config.edit.clickDelete, title: string, __fields, clickSaveAndContinue: cy.openmage.test.backend.cms.block.config.edit.clickSaveAndContinue, url: string}}
 */
test.config.edit = {
    title: 'Edit Block',
    url: 'cms_block/edit',
    __buttons: {
        save: {
            _: base._button + '[title="Save Block"]',
            __class: base.__buttons.save.__class,
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
            __class: base.__buttons.saveAndContinue.__class,
        },
        delete: {
            _: base._button + '[title="Delete Block"]',
            __class: base.__buttons.delete.__class,
        },
        back: {
            _: base.__buttons.back._,
            __class: base.__buttons.back.__class,
        },
        reset: {
            _: base.__buttons.reset._,
            __class: base.__buttons.reset.__class,
        },
    },
    __fields: test.__fields,
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
 * Configuration for "New Block" page
 * @type {{clickReset: cy.openmage.test.backend.cms.block.config.new.clickReset, __buttons: {save: string, back: string, reset: string, saveAndContinue: string}, clickBack: cy.openmage.test.backend.cms.block.config.new.clickBack, clickSave: cy.openmage.test.backend.cms.block.config.new.clickSave, title: string, __fields, clickSaveAndContinue: cy.openmage.test.backend.cms.block.config.new.clickSaveAndContinue, url: string}}
 */
test.config.new = {
    title: 'New Block',
    url: 'cms_block/new',
    __buttons: {
        save: {
            _: base._button + '[title="Save Block"]',
            __class: base.__buttons.save.__class,
        },
        saveAndContinue: {
            _: base.__buttons.saveAndContinue._,
            __class: base.__buttons.saveAndContinue.__class,
        },
        back: {
            _: base.__buttons.back._,
            __class: base.__buttons.back.__class,
        },
        reset: {
            _: base.__buttons.reset._,
            __class: base.__buttons.reset.__class,
        },
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save._, 'Save button clicked');
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
