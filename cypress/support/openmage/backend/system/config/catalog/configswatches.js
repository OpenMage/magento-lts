const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.config.catalog.configswatches;

test.config = {
    _id: '#nav-admin-system-config',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    url: 'system_config/index',
};

test.config.section = {
    _id: '#section-configswatches',
    title: 'Configurable Swatches',
    url: 'system_config/edit/section/configswatches',
};

