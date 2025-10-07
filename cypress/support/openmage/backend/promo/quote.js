const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

cy.testBackendPromoQuote = {};

cy.testBackendPromoQuote.config = {
    _id: '#nav-admin-promo-quote',
    _id_parent: '#nav-admin-promo',
    _h3: 'h3.icon-head',
    _button: base._button,
};

cy.testBackendPromoQuote.config.index = {
    title: 'Shopping Cart Price Rules',
    url: 'promo_quote/index',
    _grid: '#promo_quote_grid_table',
    __buttons: {
        add: '.form-buttons button[title="Add New Rule"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendPromoQuote.config.index.__buttons.add, 'Add New Shopping Cart Price Rules button clicked');
    },
}

cy.testBackendPromoQuote.config.edit = {
    title: 'Edit Rule',
    url: 'promo_quote/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}

cy.testBackendPromoQuote.config.new = {
    title: 'New Rule',
    url: 'promo_quote/new',
    __buttons: {
        save: base._button + '[title="Save"]',
        saveAndContinue: base._button + '[title="Save and Continue Edit"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}
