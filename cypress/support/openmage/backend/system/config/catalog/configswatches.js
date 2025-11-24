const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.config.catalog.configswatches;

/**
 * Configuration for admin system "Configurable Swatches" settings
 * @type {{_: string, _nav: string, _title: string, url: string, section: {}}}
 */
test.config = {
    _: '#nav-admin-system-config',
    _nav: '#nav-admin-system',
    _title: base._title,
    url: 'system_config/index',
    section: {},
};

/**
 * Section "Configurable Swatches"
 * @type {{title: string, url: string, _: string}}
 */
test.config.section = {
    _: '#section-configswatches',
    title: 'Configurable Swatches',
    url: 'system_config/edit/section/configswatches',
};

