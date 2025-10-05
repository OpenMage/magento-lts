const defaultConfig = {
    _id_parent: '#nav-admin-system',
    _h3: 'h3.icon-head',
}

cy.testBackendSystem = {
    cache: {
        _id: '#nav-admin-system-cache',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Cache Storage Management',
            url: 'cache/index',
            _grid: '#cache_grid_table',
            __buttons: {
                flushApply: '.form-buttons button[title="Flush & Apply Updates"]',
                flushCache: '.form-buttons button[title="Flush Cache Storage"]',
            },
        },
    },
    design: {
        _id: '#nav-admin-system-design',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Design',
            url: 'system_design/index',
            _grid: '#designGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add Design Change"]',
            },
        },
        edit: {
            title: 'Edit Design Change',
            url: 'system_design/edit',
        },
        new: {
            title: 'New Design Change',
            url: 'system_design/new',
        },
    },
    email: {
        _id: '#nav-admin-system-email_template',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Transactional Emails',
            url: 'system_email_template/index',
            _grid: '#systemEmailTemplateGrid_table',
            __buttons: {
                add: '.form-buttons button[title="Add New Template"]',
            },
        },
        edit: {
            title: 'Edit Email Template',
            url: 'system_email_template/edit',
        },
        new: {
            title: 'New Email Template',
            url: 'system_email_template/new',
        },
    },
    myaccount: {
        _id: '#nav-admin-system-myaccount',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'My Account',
            url: 'system_account/index',
            __buttons: {
                save: '.form-buttons button[title="Save Account"]',
            },
            __validation: {
                _input: {
                    username: '#username',
                    firstname: '#firstname',
                    lastname: '#lastname',
                    email: '#email',
                    current_password: '#current_password',
                }
            }

        },
    },
    manage_curreny: {
        _id: '#nav-admin-system-currency-rates',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Currency Rates',
            url: 'system_currency/index',
            __buttons: {
                save: '.form-buttons button[title="Save Currency Rates"]',
            },
            __validation: {
                _input: {
                    from: 'input[name="rate[USD][EUR]"]',
                }
            }
        },
    },
    notification: {
        _id: '#nav-admin-system-adminnotification',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Messages Inbox',
            url: 'notification/index',
            _grid: '#notificationGrid_table',
        },
    },
    indexes: {
        _id: '#nav-admin-system-index',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        list: {
            title: 'Index Management',
            url: 'process/list',
            _grid: '#indexer_processes_grid_table',
        },
    },
    stores: {
        _id: '#nav-admin-system-store',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Manage Stores',
            url: 'system_store/index',
            __buttons: {
                addWebsite: '.form-buttons button[title="Create Website"]',
                addStore: '.form-buttons button[title="Create Store"]',
                addStoreView: '.form-buttons button[title="Create Store View"]',
            },
        },
    },
    variables: {
        _id: '#nav-admin-system-variable',
        _id_parent: defaultConfig._id_parent,
        _h3: defaultConfig._h3,
        index: {
            title: 'Custom Variables',
            url: 'system_variable/index',
            _grid: '#customVariablesGrid',
            __buttons: {
                add: '.form-buttons button[title="Add New Variable"]',
            },
        },
        edit: {
            title: 'Custom Variable',
            url: 'system_variable/edit',
        },
        new: {
            title: 'New Custom Variable',
            url: 'system_variable/new',
        },
    },
    config: {
        _buttonSave: '.form-buttons button[title="Save Config"]',
        catalog: {
            configswatches: {
                _id: '#section-configswatches',
                url: 'system_config/edit/section/configswatches',
                h3: 'Configurable Swatches',
                _h3: defaultConfig._h3,
            },
            sitemap: {
                _id: '#section-sitemap',
                url: 'system_config/edit/section/sitemap',
                h3: 'Google Sitemap',
                _h3: defaultConfig._h3,
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
        },
        customers: {
            promo: {
                _id: '#section-promo',
                url: 'system_config/edit/section/promo',
                h3: 'Promotions',
                _h3: defaultConfig._h3,
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
            },
        },
    }
}
