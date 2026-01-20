const test = cy.openmage.test.backend.system.config.general.general;

/**
 * Section "General"
 * @type {{
 *      _: string,
 *      title: string,
 *      url: string,
 *      storeInformation: {}
 * }}
 */
test.config.section = {
    _: '#section-general',
    title: 'General',
    url: 'system_config/edit/section/general',
    storeInformation: {},
}

/**
 * Fields for "Store information" group
 * @type {{
 *   _: string,
 *   __fields: {}
 */
test.config.section.storeInformation = {
    _: '#general_store_information-head',
    __fields: {},
}

test.config.section.storeInformation.__fields = {
    name: {
        _: '#general_store_information_name',
        _label: '#row_general_store_information_name .scope-label',
    },
    phone: {
        _: '#general_store_information_phone',
        _label: '#row_general_store_information_phone .scope-label',
    },
    hours: {
        _: '#general_store_information_hours',
        _label: '#row_general_store_information_hours .scope-label',
    },
    address: {
        _: '#general_store_information_address',
        _label: '#row_general_store_information_address .scope-label',
    },
};
