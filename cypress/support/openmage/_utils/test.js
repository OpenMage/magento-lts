/**
 * Namespace for backend tests
 * @type {{}}
 */
cy.openmage.test.backend = {};

/**
 * Base configuration for backend tests
 * @type {{_button: string, _title: string, __buttons: {}, __buttonsSets: {}}}
 * @private
 */
cy.openmage.test.backend.__base = {
    _button: 'div.content-header .form-buttons button',
    _title: 'h3.icon-head',
    __buttons: {},
    __buttonsSets: {},
};

/**
 * Base buttons configuration for backend tests
 * @type {{add: {}, save: {}, saveAndContinue: {}, delete: {}, back: {}, reset: {}}}
 * @private
 */
cy.openmage.test.backend.__base.__buttons = {
    add: {},
    save: {},
    saveAndContinue: {},
    delete: {},
    back: {},
    reset: {},
};

/**
 * Configuration for "Add" button
 * @type {{__class: string[]}}
 */
cy.openmage.test.backend.__base.__buttons.add = {
    __class: ['scalable', 'add'],
};

/**
 * Configuration for "Save" button
 * @type {{_: string, __class: string[], click: cy.openmage.test.backend.__base.__buttons.save.click}}
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
 * @type {{_: string, __class: string[], click: cy.openmage.test.backend.__base.__buttons.saveAndContinue.click}}
 */
cy.openmage.test.backend.__base.__buttons.saveAndContinue = {
    _: cy.openmage.test.backend.__base._button + '[title="Save and Continue Edit"]',
    __class: ['scalable', 'save', 'continue'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.saveAndContinue._, 'Save and Continue button clicked');
    },
};

/**
 * Configuration for "Delete" button
 * @type {{_: string, __class: string[], click: cy.openmage.test.backend.__base.__buttons.delete.click}}
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
 * @type {{_: string, __class: string[], click: cy.openmage.test.backend.__base.__buttons.back.click}}
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
 * @type {{_: string, __class: string[], click: cy.openmage.test.backend.__base.__buttons.reset.click}}
 */
cy.openmage.test.backend.__base.__buttons.reset = {
    _: cy.openmage.test.backend.__base._button + '[title="Reset"]',
    __class: ['scalable', 'reset'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.reset._, 'Reset button clicked');
    },
};

/**
 * Configuration for "Print" button
 * @type {{__class: string[], click: cy.openmage.test.backend.__base.__buttons.print.click, _: string}}
 */
cy.openmage.test.backend.__base.__buttons.print = {
    _: cy.openmage.test.backend.__base._button + '[title="Print"]',
    __class: ['scalable', 'save', 'print'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.print._, 'Print button clicked');
    },
};

/**
 * Configuration for "Send Email" button
 * @type {{__class: string[], click: cy.openmage.test.backend.__base.__buttons.email.click, _: string}}
 */
cy.openmage.test.backend.__base.__buttons.email = {
    _: cy.openmage.test.backend.__base._button + '[title="Send Email"]',
    __class: ['scalable', 'send-email'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.email._, 'Reset button clicked');
    },
};

/**
 * Configuration for "Convert to Plain Text" button
 * @type {{__class: string[], click: cy.openmage.test.backend.__base.__buttons.convertToPlain.click, _: string}}
 */
cy.openmage.test.backend.__base.__buttons.convertToPlain = {
    _: cy.openmage.test.backend.__base._button + '[title="Convert to Plain Text"]',
    __class: ['scalable', 'task', 'to-plain'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.convertToPlain._, 'Convert to Plain Text button clicked');
    },
};

/**
 * Configuration for "Convert to Plain Text" button
 * @type {{__class: string[], click: cy.openmage.test.backend.__base.__buttons.preview.click, _: string}}
 */
cy.openmage.test.backend.__base.__buttons.preview = {
    _: cy.openmage.test.backend.__base._button + '[title="Preview Template"]',
    __class: ['scalable', 'task', 'preview'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.preview._, 'Preview Text button clicked');
    },
};

/**
 * Configuration for "Save and Apply" button
 * @type {{__class: string[], click: cy.openmage.test.backend.__base.__buttons.saveAndApply.click, _: string}}
 */
cy.openmage.test.backend.__base.__buttons.saveAndApply = {
    _: cy.openmage.test.backend.__base._button + '[title="Save and Apply"]',
    __class: ['scalable', 'apply'],
    click: () => {
        cy.openmage.tools.click(cy.openmage.test.backend.__base.__buttons.saveAndApply._, 'Save and Apply button clicked');
    },
};

/**
 * Base buttons configuration for backend tests
 * @type {{
 *      save: cy.openmage.test.backend.__base.__buttons.save,
 *      saveAndContinue: cy.openmage.test.backend.__base.__buttons.saveAndContinue,
 *      delete: cy.openmage.test.backend.__base.__buttons.delete,
 *      back: cy.openmage.test.backend.__base.__buttons.back,
 *      reset: cy.openmage.test.backend.__base.__buttons.reset,
 * }}
 */
cy.openmage.test.backend.__base.__buttonsSets.edit = {
    save: cy.openmage.test.backend.__base.__buttons.save,
    saveAndContinue: cy.openmage.test.backend.__base.__buttons.saveAndContinue,
    delete: cy.openmage.test.backend.__base.__buttons.delete,
    back: cy.openmage.test.backend.__base.__buttons.back,
    reset: cy.openmage.test.backend.__base.__buttons.reset,
};

/**
 * Base buttons configuration for backend tests without "Save and Continue Edit" button
 * @type {{
 *      save: cy.openmage.test.backend.__base.__buttons.save,
 *      delete: cy.openmage.test.backend.__base.__buttons.delete,
 *      back: cy.openmage.test.backend.__base.__buttons.back,
 *      reset: cy.openmage.test.backend.__base.__buttons.reset,
 * }}
 */
cy.openmage.test.backend.__base.__buttonsSets.editNoContinue = {
    save: cy.openmage.test.backend.__base.__buttons.save,
    delete: cy.openmage.test.backend.__base.__buttons.delete,
    back: cy.openmage.test.backend.__base.__buttons.back,
    reset: cy.openmage.test.backend.__base.__buttons.reset,
};

/**
 * Base buttons configuration for backend tests for "New" pages
 * @type {{
 *      save: cy.openmage.test.backend.__base.__buttons.save,
 *      saveAndContinue: cy.openmage.test.backend.__base.__buttons.saveAndContinue,
 *      back: cy.openmage.test.backend.__base.__buttons.back,
 *      reset: cy.openmage.test.backend.__base.__buttons.reset,
 * }}
 */
cy.openmage.test.backend.__base.__buttonsSets.new = {
    save: cy.openmage.test.backend.__base.__buttons.save,
    saveAndContinue: cy.openmage.test.backend.__base.__buttons.saveAndContinue,
    back: cy.openmage.test.backend.__base.__buttons.back,
    reset: cy.openmage.test.backend.__base.__buttons.reset,
};

// TODO: fix that pages ... add buttons with continue
/**
 * Base buttons configuration for backend tests for "New" pages without "Save and Continue Edit" button
 * @type {{
 *      save: cy.openmage.test.backend.__base.__buttons.save,
 *      back: cy.openmage.test.backend.__base.__buttons.back,
 *      reset: cy.openmage.test.backend.__base.__buttons.reset,
 * }}
 */
cy.openmage.test.backend.__base.__buttonsSets.newNoContinue = {
    save: cy.openmage.test.backend.__base.__buttons.save,
    back: cy.openmage.test.backend.__base.__buttons.back,
    reset: cy.openmage.test.backend.__base.__buttons.reset,
};

/**
 * Buttons configuration for backend sales pages
 * @type {{
 *      save: cy.openmage.test.backend.__base.__buttons.print,
 *      back: cy.openmage.test.backend.__base.__buttons.email,
 *      reset: cy.openmage.test.backend.__base.__buttons.back,
 * }}
 */
cy.openmage.test.backend.__base.__buttonsSets.sales = {
    print: cy.openmage.test.backend.__base.__buttons.print,
    email: cy.openmage.test.backend.__base.__buttons.email,
    back: cy.openmage.test.backend.__base.__buttons.back,
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
cy.openmage.test.backend.system.config.general = {};
cy.openmage.test.backend.system.config.general.general = {};
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
