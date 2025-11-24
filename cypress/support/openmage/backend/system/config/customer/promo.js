const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.config.customer.promo;

/**
 * Configuration for admin system "Promotions" settings
 * @type {{_nav: string, _title: string, section: {}, url: string, _: string}}
 */
test.config = {
    _: '#nav-admin-system-config',
    _nav: '#nav-admin-system',
    _title: base._title,
    url: 'system_config/index',
    section: {},
};

/**
 * Section "Promotions"
 * @type {{title: string, url: string, _: string}}
 */
test.config.section = {
    _: '#section-promo',
    title: 'Promotions',
    url: 'system_config/edit/section/promo',
}

/**
 * Fields for "Auto-generated Coupon Codes" group
 * @type {{_id: string, __fields: {dashes: {_: string}, length: {_: string}}}}
 */
test.config.section.couponCodes = {
    _: '#promo_auto_generated_coupon_codes-head',
    __fields: {
        length: {
            _: '#promo_auto_generated_coupon_codes_length',
        },
        dashes: {
            _: '#promo_auto_generated_coupon_codes_dash',
        },
    }
}
