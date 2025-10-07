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
}

/**
 * Configuration for "Static Blocks" menu item
 * @type {{_button: string, _title: string, _id_parent: string, _id: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-cms-block',
    _id_parent: '#nav-admin-cms',
    _title: base._title,
    _button: base._button,
    url: 'cms_block/index',
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
        add: base._button + '[title="Add New Block"]',
    },
    clickAdd: (log = 'Add CMS Blocks button clicked') => {
        tools.click(test.config.index.__buttons.add, log);
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
        save: base._button + '[title="Save Block"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete Block"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(test.config.new.__buttons.saveAndContinue, 'Save and Continue button clicked');
    },
    clickDelete: () => {
        tools.click(test.config.new.__buttons.back, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset, 'Reset button clicked');
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
        save: base._button + '[title="Save Block"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: test.__fields,
    clickSave: () => {
        tools.click(test.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(test.config.new.__buttons.saveAndContinue, 'Save and Continue button clicked');
    },
    clickBack: () => {
        tools.click(test.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(test.config.new.__buttons.reset, 'Reset button clicked');
    },
}
