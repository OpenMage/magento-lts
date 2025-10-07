const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.category;

/**
 * Configuration for category tests
 * @type {{_button: string, _title: string, _id_parent: string, _id: string, url: string}}
 */
test.config = {
    _id: '#nav-admin-catalog-categories',
    _id_parent: '#nav-admin-catalog',
    _title: '#category-edit-container h3.icon-head',
    _button: base._button,
    url: 'catalog_category/index',
}

/**
 * Configuration for category index page
 * @type {{__buttons: {save: string, reset: string}, title: string, url: string}}
 * TODO: add back button
 * TODO: rename to "Save"
 */
test.config.index = {
    title: 'New Root Category',
    url: test.config.url,
    __buttons: {
        save: base._button + '[title="Save Category"]',
        reset: base._button + '[title="Reset"]',
    },
}
