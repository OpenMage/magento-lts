const base = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystemConfig = {};

cy.testBackendSystemConfig.config = {
    _buttonSave: '.form-buttons button[title="Save Config"]',
}

cy.testBackendSystemConfig.config.catalog = {};
cy.testBackendSystemConfig.config.customers = {};

cy.testBackendSystemConfig.config.catalog.configswatches = {
    _id: '#section-configswatches',
    url: 'system_config/edit/section/configswatches',
    h3: 'Configurable Swatches',
    _h3: base._h3,
};

cy.testBackendSystemConfig.config.catalog.sitemap = {
    _id: '#section-sitemap',
    url: 'system_config/edit/section/sitemap',
    h3: 'Google Sitemap',
    _h3: base._h3,
    __validation: {
        priority: {
            _input: {
                category: '#sitemap_category_priority',
                product: '#sitemap_product_priority',
                page: '#sitemap_page_priority',
            }
        }
    }
}

cy.testBackendSystemConfig.config.customers.promo = {
    _id: '#section-promo',
    url: 'system_config/edit/section/promo',
    h3: 'Promotions',
    _h3: base._h3,
    __validation: {
        __groups: {
            couponCodes: {
                _id: '#promo_auto_generated_coupon_codes-head',
                _input: {
                    length: '#promo_auto_generated_coupon_codes_length',
                    dashes: '#promo_auto_generated_coupon_codes_dash',
                }
            }
        }
    }
}
