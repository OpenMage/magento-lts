<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml help url mapper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

abstract class Mage_Adminhtml_Helper_Help_Mapping extends Mage_Core_Helper_Abstract
{
    /**
     * Mapping of help page urls depending on module and page route
     *
     * @var array
     */
    protected $_moduleMappings = array(
        'Mage_Adminhtml' => array(
            /* Dashboard */
            'dashboard' => 'store-operations/reports-dashboard.html',
            /* Sales */
            'sales_order' => 'order-processing/order-manage.html',
            'sales_invoice' => 'order-processing/order-invoice.html',
            'sales_shipment' => 'order-processing/order-ship.html',
            'sales_creditmemo' => 'order-processing/credit-refunds.html',
            'sales_transactions' => 'order-processing/sales-transactions.html',
            'sales_recurring_profile' => 'catalog/product-recurring-profile.html',
            'sales_billing_agreement' => 'payment/paypal-billing-agreements.html',
            'checkout_agreement' => 'order-processing/terms-conditions.html',
            /* Sales → Tax */
            'tax_rule' => 'tax/tax-rules.html',
            'tax_rate' => 'tax/tax-zones-rates.html',
            'tax_rate/importExport' => 'tax/tax-rates-import-export.html',
            'tax_class_customer' => 'tax/tax-class-customer.html',
            'tax_class_product' => 'tax/tax-class-product.html',
            /* Catalog */
            'catalog_product' => 'catalog/product-create.html',
            'catalog_category/edit' => 'catalog/category-create.html',
            'urlrewrite' => 'search_seo/seo-url-redirect.html',
            'catalog_search' => 'search_seo/search-terms.html',
            'sitemap' => 'marketing/google-sitemap.html',
            /* Catalog → Attributes */
            'catalog_product_attribute' => 'catalog/attributes.html',
            'catalog_product_set' => 'catalog/attribute-set.html',
            /* Catalog → Reviews and Ratings → Customer Reviews */
            'catalog_product_review/pending' => 'marketing/product-reviews.html',
            'catalog_product_review' => 'marketing/product-reviews.html',
            'rating' => 'marketing/product-reviews-ratings.html',
            /* Catalog → Tags */
            'tag' => 'marketing/tags.html',
            'tag/pending' => 'marketing/tags-moderate.html',
            /* Customers */
            'customer' => 'customers/customer-account-create.html',
            'customer_group' => 'customers/customer-group-create.html',
            'customer_online' => 'order-processing/customers-online.html',
            /* Promotions */
            'promo_catalog' => 'marketing/price-rules-catalog.html',
            'promo_quote' => 'marketing/price-rules-shopping-cart.html',
            /* Newsletter */
            'newsletter_template' => 'marketing/newsletter-templates.html',
            'newsletter_queue' => 'marketing/newsletter-queue.html',
            'newsletter_subscriber' => 'marketing/newsletter.html',
            'newsletter_problem' => 'marketing/newsletter.html',
            /* CMS */
            'cms_page' => 'cms/pages.html',
            'cms_block' => 'cms/blocks.html',
            'poll' => 'marketing/polls.html',
            /* Reports → Sales */
            'report_sales/sales' => 'store-operations/reports-generating.html',
            'report_sales/tax' =>'store-operations/reports-available.html',
            'report_sales/invoiced' =>'store-operations/reports-available.html',
            'report_sales/shipping' =>'store-operations/reports-available.html',
            'report_sales/refunded' =>'store-operations/reports-available.html',
            'report_sales/coupons' =>'store-operations/reports-available.html',
            /* Reports → Shopping Cart */
            'report_shopcart/product' =>'store-operations/reports-available.html',
            'report_shopcart/abandoned' =>'store-operations/reports-available.html',
            /* Reports → Products */
            'report_sales/bestsellers' =>'store-operations/reports-available.html',
            'report_product/sold' =>'store-operations/reports-available.html',
            'report_product/viewed' =>'store-operations/reports-available.html',
            'report_product/lowstock' =>'store-operations/reports-available.html',
            'report_product/downloads' =>'store-operations/reports-available.html',
            /* Reports → Customers */
            'report_customer/accounts' =>'store-operations/reports-available.html',
            'report_customer/totals' =>'store-operations/reports-available.html',
            'report_customer/orders' =>'store-operations/reports-available.html',
            /* Reports → Tags */
            'report_tag/customer' =>'store-operations/reports-available.html',
            'report_tag/product' =>'store-operations/reports-available.html',
            'report_tag/popular' =>'store-operations/reports-available.html',
            /* Reports → Reviews */
            'report_review/customer' =>'store-operations/reports-available.html',
            'report_review/product' =>'store-operations/reports-available.html',
            /* Search Terms */
            'report/search' =>'store-operations/reports-available.html',
            /* Refresh statistics */
            'report_statistics' =>'store-operations/reports-refresh.html',
            /* My Account */
            'system_account' => 'store-operations/admin-my-account.html',
            /* Notifications */
            'notification' => 'store-operations/admin-messages.html',
            /* Tools → Backup */
            'system_backup' => 'system-operations/server-backup-rollback.html',
            /* Web Services */
            'api_user' => 'system-operations/web-services.html',
            'api_role' => 'system-operations/web-services.html',
            'api2_role' => 'system-operations/web-services.html',
            /* System → Design */
            'system_design' => 'design/themes.html',
            /* System → Import/Export */
            'system_convert_gui' => 'store-operations/dataflow.html',
            'system_convert_profile' => 'store-operations/dataflow.html',
            /* System → Manage Currency */
            'system_currency' => 'store-operations/currency-rates.html',
            'system_currencysymbol' => 'store-operations/currency-symbols.html',
            /* System → Transactional emails */
            'system_email_template' => 'store-operations/email-transactional.html',
            /* System → Custom Variables */
            'system_variable' => 'cms/variables-custom.html',
            /* System → Permissions */
            'permissions_user' => 'store-operations/permissions-user-new.html',
            'permissions_role' => 'store-operations/permissions-role-custom.html',
            /* System → Cache Management */
            'cache' => 'system-operations/cache-management.html',
            /* System → Stores Management */
            'system_store' => 'store-operations/store-hierarchy.html',
            /* System → Order statuses */
            'sales_order_status' => 'order-processing/order-status.html',
            /* System → Configuration */
            'system_config/edit/section/general' => 'configuration/general/general.html',
            'system_config/edit/section/web' => 'configuration/general/web.html',
            'system_config/edit/section/design' => 'configuration/general/design.html',
            'system_config/edit/section/currency' => 'configuration/general/currency-setup.html',
            'system_config/edit/section/trans_email' => 'configuration/general/store-email-addresses.html',
            'system_config/edit/section/contacts' => 'configuration/general/contacts.html',
            'system_config/edit/section/reports' => 'configuration/general/reports.html',
            'system_config/edit/section/cms' => 'configuration/general/content-management.html',
            'system_config/edit/section/catalog' => 'configuration/catalog/catalog.html',
            'system_config/edit/section/configswatches' => 'configuration/catalog/configurable-swatches.html',
            'system_config/edit/section/cataloginventory' => 'configuration/catalog/inventory.html',
            'system_config/edit/section/sitemap' => 'configuration/catalog/google-sitemap.html',
            'system_config/edit/section/rss' => 'configuration/catalog/rss-feeds.html',
            'system_config/edit/section/sendfriend' => 'configuration/catalog/email-to-a-friend.html',
            'system_config/edit/section/newsletter' => 'configuration/customers/newsletter.html',
            'system_config/edit/section/customer' => 'configuration/customers/customer-configuration.html',
            'system_config/edit/section/wishlist' => 'configuration/customers/wishlist.html',
            'system_config/edit/section/promo' => 'configuration/customers/promotions.html',
            'system_config/edit/section/persistent' => 'configuration/customers/persistent-shopping-cart.html',
            'system_config/edit/section/sales' => 'configuration/sales/sales.html',
            'system_config/edit/section/sales_email' => 'configuration/sales/sales-emails.html',
            'system_config/edit/section/sales_pdf' => 'configuration/sales/pdf-print-outs.html',
            'system_config/edit/section/tax' => 'configuration/sales/tax.html',
            'system_config/edit/section/checkout' => 'configuration/sales/checkout.html',
            'system_config/edit/section/shipping' => 'configuration/sales/shipping-settings.html',
            'system_config/edit/section/carriers' => 'configuration/sales/shipping-methods.html',
            'system_config/edit/section/google' => 'configuration/sales/google-api.html',
            'system_config/edit/section/payment' => 'configuration/sales/payment-methods.html',
            'system_config/edit/section/payment_services' => 'configuration/sales/payment-services.html',
            'system_config/edit/section/moneybookers' => 'payment/gateways.html',
            'system_config/edit/section/api' => 'configuration/services/magento-core-api.html',
            'system_config/edit/section/oauth' => 'configuration/services/oauth.html',
            'system_config/edit/section/admin' => 'configuration/advanced/admin.html',
            'system_config/edit/section/system' => 'configuration/advanced/system.html',
            'system_config/edit/section/advanced' => 'configuration/advanced/advanced.html',
            'system_config/edit/section/dev' => 'configuration/advanced/developer.html',
        ),
        'Mage_Widget_Adminhtml' => array(
            'widget_instance' => 'cms/widgets.html',
        ),
        'Mage_Paypal_Adminhtml' => array(
            'paypal_reports' => 'store-operations/reports-available.html',
        ),
        'Mage_Compiler_Adminhtml' => array(
            'compiler_process' => 'system-operations/system-tools-compilation.html',
        ),
        'Mage_Api2_Adminhtml' => array(
            'api2_attribute' => 'system-operations/web-services.html',
        ),
        'Mage_Oauth_Adminhtml' => array(
            'oauth_consumer' => 'system-operations/web-services.html',
            'oauth_authorizedTokens' => 'system-operations/web-services.html',
            'oauth_admin_token' => 'system-operations/web-services.html',
        ),
        'Mage_ImportExport_Adminhtml' => array(
            'import' => 'store-operations/data-import.html',
            'export' => 'store-operations/data-export.html',
        ),
        'Mage_Connect_Adminhtml' => array(
            'extension_custom/edit' => 'magento/magento-connect.html'
        ),
        'Mage_Index_Adminhtml' => array(
            'process/list' => 'system-operations/index-management.html'
        ),
    );

    /**
     * Compose reconstructed URL using mapping
     *
     * @param string $frontModule
     * @param string $controllerName
     * @param string $actionName
     * @return string|bool
     */
    protected function findInMapping($frontModule, $controllerName, $actionName)
    {
        if ($actionName === 'index' ) {
            $targetToFind = $controllerName;
        } else {
            $targetToFind = $controllerName . '/' . $actionName;
        }
        if (isset($this->_moduleMappings[$frontModule])
            && isset($this->_moduleMappings[$frontModule][$targetToFind])
        ) {
            return $this->_moduleMappings[$frontModule][$targetToFind];
        }

        return false;
    }

    /**
     * Determine which version of docs should target onto
     *
     * @return string
     */
    protected function getHelpTargetVersion()
    {
        return Mage::getConfig()->getNode('default/help/target_version');
    }

}
