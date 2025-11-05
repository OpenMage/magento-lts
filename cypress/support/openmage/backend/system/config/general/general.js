const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.config.general.general;

/**
 * Configuration for admin system "General" settings
 * @type {{_: string, _nav: string, _title: string, url: string, section: {}}}
 */
test.config = {
    _: '#nav-admin-system-config',
    _nav: '#nav-admin-system',
    _title: base._title,
    url: 'system_config/index',
    section: {},
}

/**
 * Section "General"
 * @type {{_: string, title: string, url: string}}
 */
test.config.section = {
    _: '#section-general',
    title: 'General',
    url: 'system_config/edit/section/general',
}

/**
 * Fields for "Store information" group
 * @type {{__fields: {name: {_: string}, phone: {_: string}, hours: {_: string}, address: {_: string}}}}
 */
test.config.section.storeInformation = {
    _: '#general_store_information-head',
    __fields: {
        name: {
            _: '#general_store_information_name',
            label: '#row_general_store_information_name .scope-label',
        },
        phone: {
            _: '#general_store_information_phone',
            label: '#row_general_store_information_phone .scope-label',
        },
        hours: {
            _: '#general_store_information_hours',
            label: '#row_general_store_information_hours .scope-label',
        },
        address: {
            _: '#general_store_information_address',
            label: '#row_general_store_information_address .scope-label',
        },
    }
};
