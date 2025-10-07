const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
};

base.__fields = {
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

cy.testBackendCmsBlock = {};

cy.testBackendCmsBlock.config = {
    _id: '#nav-admin-cms-block',
    _id_parent: '#nav-admin-cms',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendCmsBlock.config.index = {
    title: 'Static Blocks',
    url: 'cms_block/index',
    _grid: '#cmsBlockGrid_table',
    __buttons: {
        add: base._button + '[title="Add New Block"]',
    },
    clickAdd: (log = 'Add CMS Blocks button clicked') => {
        tools.click(cy.testBackendCmsBlock.config.index.__buttons.add, log);
    },
}

cy.testBackendCmsBlock.config.edit = {
    title: 'Edit Block',
    url: 'cms_block/edit',
    __buttons: {
        save: base._button + '[title="Save Block"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete Block"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.saveAndContinue, 'Save and Continue button clicked');
    },
    clickDelete: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.back, 'Delete button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.reset, 'Reset button clicked');
    },
}

cy.testBackendCmsBlock.config.new = {
    title: 'New Block',
    url: 'cms_block/new',
    __buttons: {
        save: base._button + '[title="Save Block"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
    __fields: base.__fields,
    clickSave: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.save, 'Save button clicked');
    },
    clickSaveAndContinue: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.saveAndContinue, 'Save and Continue button clicked');
    },
    clickBack: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.back, 'Back button clicked');
    },
    clickReset: () => {
        tools.click(cy.testBackendCmsBlock.config.new.__buttons.reset, 'Reset button clicked');
    },
}
