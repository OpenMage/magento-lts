export const adminNav = {
    catalog: '#nav-admin-catalog',
    cms: '#nav-admin-cms',
    customer: '#nav-admin-customer',
    newsletter: '#nav-admin-newsletter',
    promo: '#nav-admin-promo',
    sales: '#nav-admin-sales',
    system: '#nav-admin-system',
}

export const paths = {
    backend: {
        catalog: {
            products: {
                parent: adminNav.catalog,
                id: '#nav-admin-catalog-products',
                url: 'catalog_product/index',
                h3: 'Manage Products',
            },
            categories: {
                parent: adminNav.catalog,
                id: '#nav-admin-catalog-categories',
                url: 'catalog_category/index',
                h3: 'Manage Categories',
            },
            search: {
                parent: adminNav.catalog,
                id: '#nav-admin-catalog-search',
                url: 'catalog_search/index',
                h3: 'Search',
            },
            sitemap: {
                parent: adminNav.catalog,
                id: '#nav-admin-catalog-sitemap',
                url: 'sitemap/index',
                h3: 'Google Sitemap',
            },
            urlrewrite: {
                parent: adminNav.catalog,
                id: '#nav-admin-catalog-urlrewrite',
                url: 'urlrewrite/index',
                h3: 'URL Rewrite Management',
            }
        },
        cms: {
            block: {
                parent: adminNav.cms,
                id: '#nav-admin-cms-block',
                url: 'cms_block/index',
                h3: 'Static Blocks',
            },
            page: {
                parent: adminNav.cms,
                id: '#nav-admin-cms-page',
                url: 'cms_page/index',
                h3: 'Manage Pages',
            },
            widget: {
                parent: adminNav.cms,
                id: '#nav-admin-cms-widget_instance',
                url: 'widget_instance/index',
                h3: 'Manage Widget Instances',
            }
        },
        customers: {
            manage: {
                parent: adminNav.customer,
                id: '#nav-admin-customer-manage',
                url: 'customer/index',
                h3: 'Manage Customers',
            },
            groups: {
                parent: adminNav.customer,
                id: '#nav-admin-customer-group',
                url: 'customer_group/index',
                h3: 'Customer Groups',
            },
            online: {
                parent: adminNav.customer,
                id: '#nav-admin-customer-online',
                url: 'customer_online/index',
                h3: 'Online Customers',
            }
        },
        newsletter: {
            templates: {
                parent: adminNav.newsletter,
                id: '#nav-admin-newsletter-template',
                url: 'newsletter_template/index',
                h3: 'Newsletter Templates',
            },
            queue: {
                parent: adminNav.newsletter,
                id: '#nav-admin-newsletter-queue',
                url: 'newsletter_queue/index',
                h3: 'Newsletter Queue',
            },
            subscriber: {
                parent: adminNav.newsletter,
                id: '#nav-admin-newsletter-subscriber',
                url: 'newsletter_subscriber/index',
                h3: 'Newsletter Subscribers',
            },
            report: {
                parent: adminNav.newsletter,
                id: '#nav-admin-newsletter-problem',
                url: 'newsletter_problem/index',
                h3: 'Newsletter Problem Reports',
            }
        },
        promo: {
            catalog: {
                parent: adminNav.promo,
                id: '#nav-admin-promo-catalog',
                url: 'promo_catalog/index',
                h3: 'Catalog Price Rules',
            },
            cart: {
                parent: adminNav.promo,
                id: '#nav-admin-promo-quote',
                url: 'promo_quote/index',
                h3: 'Shopping Cart Price Rules',
            }
        },
        sales: {
            creditmemo: {
                parent: adminNav.sales,
                id: '#nav-admin-sales-creditmemo',
                url: 'sales_creditmemo/index',
                h3: 'Credit Memos',
            },
            invoice: {
                parent: adminNav.sales,
                id: '#nav-admin-sales-invoice',
                url: 'sales_invoice/index',
                h3: 'Invoice',
            },
            order: {
                parent: adminNav.sales,
                id: '#nav-admin-sales-order',
                url: 'sales_order/index',
                h3: 'Orders',
            },
            shipment: {
                parent: adminNav.sales,
                id: '#nav-admin-sales-shipment',
                url: 'sales_shipment/index',
                h3: 'Shipments',
            },
            transactions: {
                parent: adminNav.sales,
                id: '#nav-admin-sales-transactions',
                url: 'sales_transactions/index',
                h3: 'Transactions',
            }
        },
        system: {
            cache: {
                parent: adminNav.system,
                id: '#nav-admin-system-cache',
                url: 'cache/index',
                h3: 'Cache Storage Management',
            },
            design: {
                parent: adminNav.system,
                id: '#nav-admin-system-design',
                url: 'system_design/index',
                h3: 'Design',
            },
            email: {
                parent: adminNav.system,
                id: '#nav-admin-system-email_template',
                url: 'system_email_template/index',
                h3: 'Transactional Emails',
            },
            myaccount: {
                parent: adminNav.system,
                id: '#nav-admin-system-myaccount',
                url: 'system_account/index',
                h3: 'My Account',
            },
            notification: {
                parent: adminNav.system,
                id: '#nav-admin-system-adminnotification',
                url: 'notification/index',
                h3: 'Messages Inbox',
            },
            indexes: {
                parent: adminNav.system,
                id: '#nav-admin-system-index',
                url: 'process/list',
                h3: 'Index Management',
            },
            stores: {
                parent: adminNav.system,
                id: '#nav-admin-system-store',
                url: 'system_store/index',
                h3: 'Manage Stores',
            },
            variables: {
                parent: adminNav.system,
                id: '#nav-admin-system-variable',
                url: 'system_variable/index',
                h3: 'Custom Variables',
            },
            config: {
                catalog: {
                    configswatches: {
                        id: '#section-configswatches',
                        url: 'system_config/edit/section/configswatches',
                        h3: 'Configurable Swatches',
                    },
                    sitemap: {
                        id: '#section-sitemap',
                        url: 'system_config/edit/section/sitemap',
                        h3: 'Google Sitemap',
                    }
                },
                customers: {
                    promo: {
                        id: '#section-promo',
                        url: 'system_config/edit/section/promo',
                        h3: 'Promotions',
                    },
                },
            }
        }
    }
}
