const base = cy.openmage.test.backend.__base;
const test = cy.openmage.test.backend.system.config.customer.promo;

test.config = {
    _id: '#nav-admin-system-config',
    _id_parent: '#nav-admin-system',
    _title: base._title,
    url: 'system_config/index',
};

test.config.section = {
    _id: '#section-promo',
    title: 'Promotions',
    url: 'system_config/edit/section/promo',
    __validation: {
        __groups: {
            couponCodes: {
                _id: '#promo_auto_generated_coupon_codes-head',
                __fields: {
                    length: {
                        _: '#promo_auto_generated_coupon_codes_length',
                    },
                    dashes: {
                        _: '#promo_auto_generated_coupon_codes_dash',
                    },
                }
            }
        }
    }
}
