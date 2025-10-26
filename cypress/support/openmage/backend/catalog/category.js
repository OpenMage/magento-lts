const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.catalog.category;

/**
 * Configuration for category tests
 * @type {{_: string, _id_parent: string, _title: string, _button: string, url: string, index: {}}}
 */
test.config = {
    _: '#nav-admin-catalog-categories',
    _nav: '#nav-admin-catalog',
    _title: '#category-edit-container h3.icon-head',
    _button: base._button,
    url: 'catalog_category/index',
    index: {},
}

/**
 * Configuration for category index page
 * @type {{__buttons: {save: {_: string}, reset: {_: string}}, title: string, url: string}}
 * TODO: add back button
 * TODO: rename to "Save"
 */
test.config.index = {
    title: 'New Root Category',
    url: test.config.url,
    __buttons: {
        save: base.__buttons.save,
        reset: base.__buttons.reset,
    },
}
