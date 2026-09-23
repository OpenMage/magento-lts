const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.variable;
const tools = cy.openmage.tools;

/**
 * Configuration for "Custom Variables" section
 * @type {{_: string, _nav: string, _title: string, _button: string, __fixture: string, url: string, index: {}, edit: {}, new: {}}}
 */
test.config = {
    _: '#nav-admin-system-variable',
    _nav: '#nav-admin-system',
    _title: base._title,
    _button: base._button,
    __fixture: 'backend/system/variable',
    url: 'admin/system_variable',
    index: {},
    edit: {},
    new: {},
}

/**
 * Configuration for "Custom Variables" page
 * @type {{title: string, url: string, grid: {}}}
 */
test.config.index = {
    title: 'Custom Variables',
    url: test.config.url,
    grid: {...base.__grid, ...{ sort: { order: 'variable_id', dir: 'asc' } }},
}

/**
 * Configuration for "Edit Custom Variable" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.edit}}
 */
test.config.edit = {
    title: 'Custom Variable',
    url: 'system_variable/edit',
    __buttons: base.__buttonsSets.edit,
}

/**
 * Configuration for "New Custom Variable" page
 * @type {{title: string, url: string, __buttons: cy.openmage.test.backend.__base.__buttonsSets.new}}
 */
test.config.new = {
    title: 'New Custom Variable',
    url: 'system_variable/new',
    __buttons: base.__buttonsSets.new,
}
