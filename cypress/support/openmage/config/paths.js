const adminNav = {
    catalog: '#nav-admin-catalog',
    cms: '#nav-admin-cms',
    customer: '#nav-admin-customer',
    newsletter: '#nav-admin-newsletter',
    promo: '#nav-admin-promo',
    sales: '#nav-admin-sales',
    system: '#nav-admin-system',
}

const adminPage = {
    _h3: 'h3.icon-head',
}

cy.testRoutes = {
    api: {},
    backendLogin: {},
    backend: {
        dashboard: {
            _id: '#nav-admin-dashboard',
            url: 'dashboard/index',
            h3: 'Dashboard',
            _h3: adminPage._h3,
        },
        catalog: {
            products: {
                _id_parent: adminNav.catalog,
                _id: '#nav-admin-catalog-products',
                url: 'catalog_product/index',
                h3: 'Manage Products',
                _h3: adminPage._h3,
            },
            categories: {
                _id_parent: adminNav.catalog,
                _id: '#nav-admin-catalog-categories',
                url: 'catalog_category/index',
                h3: 'New Root Category',
                _h3: '#category-edit-container ' + adminPage._h3,
            },
            search: {
                _id_parent: adminNav.catalog,
                _id: '#nav-admin-catalog-search',
                url: 'catalog_search/index',
                h3: 'Search',
                _h3: adminPage._h3,
            },
            sitemap: {
                _id_parent: adminNav.catalog,
                _id: '#nav-admin-catalog-sitemap',
                url: 'sitemap/index',
                h3: 'Google Sitemap',
                _h3: adminPage._h3,
            },
            urlrewrite: {
                _id_parent: adminNav.catalog,
                _id: '#nav-admin-catalog-urlrewrite',
                url: 'urlrewrite/index',
                h3: 'URL Rewrite Management',
                _h3: adminPage._h3,
            }
        },
        cms: {
            block: {
                _id_parent: adminNav.cms,
                _id: '#nav-admin-cms-block',
                url: 'cms_block/index',
                h3: 'Static Blocks',
                _h3: adminPage._h3,
            },
            page: {
                _id_parent: adminNav.cms,
                _id: '#nav-admin-cms-page',
                url: 'cms_page/index',
                h3: 'Manage Pages',
                _h3: adminPage._h3,
            },
            widget: {
                _id_parent: adminNav.cms,
                _id: '#nav-admin-cms-widget_instance',
                url: 'widget_instance/index',
                h3: 'Manage Widget Instances',
                _h3: adminPage._h3,
            }
        },
        customers: {
            manage: {
                _id_parent: adminNav.customer,
                _id: '#nav-admin-customer-manage',
                url: 'customer/index',
                h3: 'Manage Customers',
                _h3: adminPage._h3,
            },
            groups: {
                _id_parent: adminNav.customer,
                _id: '#nav-admin-customer-group',
                url: 'customer_group/index',
                h3: 'Customer Groups',
                _h3: adminPage._h3,
            },
            online: {
                _id_parent: adminNav.customer,
                _id: '#nav-admin-customer-online',
                url: 'customer_online/index',
                h3: 'Online Customers',
                _h3: adminPage._h3,
            }
        },
        newsletter: {
            templates: {
                _id_parent: adminNav.newsletter,
                _id: '#nav-admin-newsletter-template',
                url: 'newsletter_template/index',
                h3: 'Newsletter Templates',
                _h3: adminPage._h3,
            },
            queue: {
                _id_parent: adminNav.newsletter,
                _id: '#nav-admin-newsletter-queue',
                url: 'newsletter_queue/index',
                h3: 'Newsletter Queue',
                _h3: adminPage._h3,
            },
            subscriber: {
                _id_parent: adminNav.newsletter,
                _id: '#nav-admin-newsletter-subscriber',
                url: 'newsletter_subscriber/index',
                h3: 'Newsletter Subscribers',
                _h3: adminPage._h3,
            },
            report: {
                _id_parent: adminNav.newsletter,
                _id: '#nav-admin-newsletter-problem',
                url: 'newsletter_problem/index',
                h3: 'Newsletter Problem Reports',
                _h3: adminPage._h3,
            }
        },
        promo: {
            catalog: {
                _id_parent: adminNav.promo,
                _id: '#nav-admin-promo-catalog',
                url: 'promo_catalog/index',
                h3: 'Catalog Price Rules',
                _h3: adminPage._h3,
            },
            cart: {
                _id_parent: adminNav.promo,
                _id: '#nav-admin-promo-quote',
                url: 'promo_quote/index',
                h3: 'Shopping Cart Price Rules',
                _h3: adminPage._h3,
            }
        },
        sales: {
            creditmemo: {
                _id_parent: adminNav.sales,
                _id: '#nav-admin-sales-creditmemo',
                url: 'sales_creditmemo/index',
                h3: 'Credit Memos',
                _h3: adminPage._h3,
            },
            invoice: {
                _id_parent: adminNav.sales,
                _id: '#nav-admin-sales-invoice',
                url: 'sales_invoice/index',
                h3: 'Invoice',
                _h3: adminPage._h3,
            },
            order: {
                _id_parent: adminNav.sales,
                _id: '#nav-admin-sales-order',
                url: 'sales_order/index',
                h3: 'Orders',
                _h3: adminPage._h3,
            },
            shipment: {
                _id_parent: adminNav.sales,
                _id: '#nav-admin-sales-shipment',
                url: 'sales_shipment/index',
                h3: 'Shipments',
                _h3: adminPage._h3,
            },
            transactions: {
                _id_parent: adminNav.sales,
                _id: '#nav-admin-sales-transactions',
                url: 'sales_transactions/index',
                h3: 'Transactions',
                _h3: adminPage._h3,
            }
        },
        system: {
            cache: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-cache',
                url: 'cache/index',
                h3: 'Cache Storage Management',
                _h3: adminPage._h3,
            },
            design: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-design',
                url: 'system_design/index',
                h3: 'Design',
                _h3: adminPage._h3,
            },
            email: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-email_template',
                url: 'system_email_template/index',
                h3: 'Transactional Emails',
                _h3: adminPage._h3,
            },
            myaccount: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-myaccount',
                url: 'system_account/index',
                h3: 'My Account',
                _h3: adminPage._h3,
                _buttonSave: '.form-buttons button[title="Save Account"]',
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
            manage_curreny: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-currency-rates',
                url: 'system_currency/index',
                h3: 'Manage Currency Rates',
                _h3: adminPage._h3,
                _buttonSave: '.form-buttons button[title="Save Currency Rates"]',
                __validation: {
                    _input: {
                        from: 'input[name="rate[USD][EUR]"]',
                    }
                }
            },
            notification: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-adminnotification',
                url: 'notification/index',
                h3: 'Messages Inbox',
                _h3: adminPage._h3,
            },
            indexes: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-index',
                url: 'process/list',
                h3: 'Index Management',
                _h3: adminPage._h3,
            },
            stores: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-store',
                url: 'system_store/index',
                h3: 'Manage Stores',
                _h3: adminPage._h3,
            },
            variables: {
                _id_parent: adminNav.system,
                _id: '#nav-admin-system-variable',
                url: 'system_variable/index',
                h3: 'Custom Variables',
                _h3: adminPage._h3,
            },
            config: {
                _buttonSave: '.form-buttons button[title="Save Config"]',
                catalog: {
                    configswatches: {
                        _id: '#section-configswatches',
                        url: 'system_config/edit/section/configswatches',
                        h3: 'Configurable Swatches',
                        _h3: adminPage._h3,
                    },
                    sitemap: {
                        _id: '#section-sitemap',
                        url: 'system_config/edit/section/sitemap',
                        h3: 'Google Sitemap',
                        _h3: adminPage._h3,
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
                        _h3: adminPage._h3,
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
    },
    frontend: {
        homepage: {
            url: '/',
            newsletter: {
                _buttonSubmit: '#newsletter-validate-detail button[type="submit"]',
                _id: '#newsletter'
            }
        },
        customer: {
            account: {
                create: {
                    url: '/customer/account/create',
                    _buttonSubmit: '#form-validate button[type="submit"]',
                    _h1: 'h1',
                    h1: 'Create an Account',
                    __validation: {
                        _input: {
                            firstname: '#firstname',
                            lastname: '#lastname',
                            email_address: '#email_address',
                            password: '#password',
                            confirmation: '#confirmation',
                        }
                    }
                }
            }
        }
    }
}
