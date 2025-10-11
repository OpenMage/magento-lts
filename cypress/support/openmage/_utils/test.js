/**
 * Namespace for backend tests
 * @type {{}}
 */
cy.openmage.test.backend = {};

/**
 * Base configuration for backend tests
 * @type {{_button: string, _title: string, __buttons: {save: string, back: string, reset: string, saveAndContinue: string, delete: string}, __buttonsNoContinue: {save: string, back: string, reset: string, delete: string}, __buttonsNew: {save: string, back: string, reset: string, saveAndContinue: string}, __buttonsNewNoContinue: {save: string, back: string, reset: string}}}
 * @private
 */
cy.openmage.test.backend.__base = {
    _button: '.form-buttons button',
    _title: 'h3.icon-head',
    __buttons: {},
    __buttonsNoContinue: {},
    __buttonsNew: {},
    __buttonsNewNoContinue: {},
};

/**
 * Base buttons configuration for backend tests
 * @type {{save: *, saveAndContinue: *, delete: *, back: *, reset: *}}
 * @private
 */
cy.openmage.test.backend.__base.__buttons = {
    save: {},
    saveAndContinue: {},
    delete: {},
    back: {},
    reset: {},
};

/**
 * Configuration for "Save" button
 * @type {{_: string, click: cy.openmage.test.backend.__base.__buttons.save.click}}
 */
cy.openmage.test.backend.__base.__buttons.save = {
    _: cy.openmage.test.backend.__base._button + '[title="Save"]',
    __class: ['scalable', 'save'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.save._, 'Save button clicked');
    },
};

/**
 * Configuration for "Save and Continue Edit" button
 * @type {{_: string, click: cy.openmage.test.backend.__base.__buttons.saveAndContinue.click}}
 */
cy.openmage.test.backend.__base.__buttons.saveAndContinue = {
    _: cy.openmage.test.backend.__base._button + '[title="Save and Continue Edit"]',
    // TODO: add class 'save-continue' to the button in OpenMage
    __class: ['scalable', 'save', 'save-continue'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.saveAndContinue._, 'Save and Continue button clicked');
    },
};

/**
 * Configuration for "Delete" button
 * @type {{_: string, click: cy.openmage.test.backend.__base.__buttons.delete.click}}
 */
cy.openmage.test.backend.__base.__buttons.delete = {
    _: cy.openmage.test.backend.__base._button + '[title="Delete"]',
    __class: ['scalable', 'delete'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.delete._, 'Delete button clicked');
    },
};

/**
 * Configuration for "Back" button
 * @type {{_: string, click: cy.openmage.test.backend.__base.__buttons.back.click}}
 */
cy.openmage.test.backend.__base.__buttons.back = {
    _: cy.openmage.test.backend.__base._button + '[title="Back"]',
    __class: ['scalable', 'back'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.back._, 'Back button clicked');
    },
};

/**
 * Configuration for "Reset" button
 * @type {{_: string, click: cy.openmage.test.backend.__base.__buttons.reset.click}}
 */
cy.openmage.test.backend.__base.__buttons.reset = {
    _: cy.openmage.test.backend.__base._button + '[title="Reset"]',
    // TODO: add class 'reset' to the button in OpenMage
    __class: ['scalable, reset'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.reset._, 'Reset button clicked');
    },
};

// TODO: fix that pages ... add buttons with continue
/**
 * Base buttons configuration for backend tests without "Save and Continue Edit" button
 * @type {{save: string, back: string, reset: string, delete: string}}
 * @private
 */
cy.openmage.test.backend.__base.__buttonsNoContinue = {
    save: cy.openmage.test.backend.__base.__buttons.save,
    delete: cy.openmage.test.backend.__base.__buttons.delete,
    back: cy.openmage.test.backend.__base.__buttons.back,
    reset: cy.openmage.test.backend.__base.__buttons.reset,
};

/**
 * Base buttons configuration for backend tests for "New" pages
 * @type {{save: string, back: string, reset: string, saveAndContinue: string}}
 * @private
 */
cy.openmage.test.backend.__base.__buttonsNew = {
    save: cy.openmage.test.backend.__base.__buttons.save,
    saveAndContinue: cy.openmage.test.backend.__base.__buttons.saveAndContinue,
    back: cy.openmage.test.backend.__base.__buttons.back,
    reset: cy.openmage.test.backend.__base.__buttons.reset,
};

// TODO: fix that pages ... add buttons with continue
/**
 * Base buttons configuration for backend tests for "New" pages without "Save and Continue Edit" button
 * @type {{save: string, back: string, reset: string}}
 * @private
 */
cy.openmage.test.backend.__base.__buttonsNewNoContinue = {
    save: cy.openmage.test.backend.__base.__buttons.save,
    back: cy.openmage.test.backend.__base.__buttons.saveAndContinue,
    reset: cy.openmage.test.backend.__base.__buttons.reset,
};

/**
 * Namespace for backend tests
 * @type {{}}
 */
cy.openmage.test.backend.dashbord = {};
cy.openmage.test.backend.catalog = {};
cy.openmage.test.backend.catalog.category = {};
cy.openmage.test.backend.catalog.product = {};
cy.openmage.test.backend.catalog.search = {};
cy.openmage.test.backend.catalog.sitemap = {};
cy.openmage.test.backend.catalog.urlRewrite = {};
cy.openmage.test.backend.cms = {};
cy.openmage.test.backend.cms.block = {};
cy.openmage.test.backend.cms.page = {};
cy.openmage.test.backend.cms.widget = {};
cy.openmage.test.backend.customer = {};
cy.openmage.test.backend.customer.customer = {};
cy.openmage.test.backend.customer.group = {};
cy.openmage.test.backend.customer.online = {};
cy.openmage.test.backend.newsletter = {};
cy.openmage.test.backend.newsletter.queue = {};
cy.openmage.test.backend.newsletter.report = {};
cy.openmage.test.backend.newsletter.subscriber = {};
cy.openmage.test.backend.newsletter.template = {};
cy.openmage.test.backend.promo = {};
cy.openmage.test.backend.promo.catalog = {};
cy.openmage.test.backend.promo.quote = {};
cy.openmage.test.backend.reports = {}; // TODO: add reports tests
cy.openmage.test.backend.sales = {};
cy.openmage.test.backend.sales.creditmemo = {};
cy.openmage.test.backend.sales.invoice = {};
cy.openmage.test.backend.sales.order = {};
cy.openmage.test.backend.sales.shipment = {};
cy.openmage.test.backend.sales.transaction = {};
cy.openmage.test.backend.system = {};
cy.openmage.test.backend.system.account = {};
cy.openmage.test.backend.system.cache = {};
cy.openmage.test.backend.system.config = {
    _buttonSave: cy.openmage.test.backend.__base._button + '[title="Save Config"]',
    clickSave: (log = 'Save config button clicked') => {
        cy.get(cy.openmage.test.backend.system.config._buttonSave)
            .first().should('be.visible')
            .click({ force: false, multiple: false });
    },
};
cy.openmage.test.backend.system.config.catalog = {};
cy.openmage.test.backend.system.config.catalog.configswatches = {};
cy.openmage.test.backend.system.config.catalog.sitemap = {};
cy.openmage.test.backend.system.config.customer = {};
cy.openmage.test.backend.system.config.customer.promo = {};
cy.openmage.test.backend.system.currency = {};
cy.openmage.test.backend.system.design = {};
cy.openmage.test.backend.system.email = {};
cy.openmage.test.backend.system.indexer = {};
cy.openmage.test.backend.system.notification = {};
cy.openmage.test.backend.system.store = {};
cy.openmage.test.backend.system.variable = {};

/**
 * Namespace for frontend tests
 * @type {{}}
 */
cy.openmage.test.frontend = {};
cy.openmage.test.frontend.customer = {};
cy.openmage.test.frontend.customer.account = {};
cy.openmage.test.frontend.homepage = {
    _url: '/',
};
cy.openmage.test.frontend.homepage.newsletter = {};
cy.openmage.test.frontend.sales = {};
cy.openmage.test.frontend.sales.guest = {};
