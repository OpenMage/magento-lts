const tools = cy.openmage.tools;

const base = {
    _button: '.form-buttons button',
}

cy.testBackendUrlrewrite = {};

cy.testBackendUrlrewrite.config = {
    _id: '#nav-admin-catalog-urlrewrite',
    _id_parent: '#nav-admin-catalog',
    _h3: 'h3.icon-head',
    _button: base._button,
}

cy.testBackendUrlrewrite.config.index = {
    title: 'URL Rewrite Management',
    url: 'urlrewrite/index',
    _grid: '#urlrewriteGrid_table',
    __buttons: {
        add: base._button + '[title="Add URL Rewrite"]',
    },
    clickAdd: () => {
        tools.click(cy.testBackendUrlrewrite.config.index.__buttons.add, 'Add URL Rewrite button clicked');
    },
}

cy.testBackendUrlrewrite.config.edit = {
    title: 'Edit URL Rewrite',
    url: 'urlrewrite/edit',
    __buttons: {
        save: base._button + '[title="Save"]',
        delete: base._button + '[title="Delete"]',
        back: base._button + '[title="Back"]',
        reset: base._button + '[title="Reset"]',
    },
}

cy.testBackendUrlrewrite.config.new = {
    title: 'Add New URL Rewrite',
    url: 'urlrewrite/edit',
    __buttons: {
        back: base._button + '[title="Back"]',
    },
}
