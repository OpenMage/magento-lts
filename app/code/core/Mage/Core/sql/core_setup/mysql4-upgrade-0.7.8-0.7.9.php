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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$configValuesMap = array(
    'catalog/productalert/email_stock_template'         => 'catalog_productalert_email_stock_template',
    'catalog/productalert/email_price_template'         => 'catalog_productalert_email_price_template',
    'catalog/productalert_cron/error_email_template'    => 'catalog_productalert_cron_error_email_template',
    'contacts/email/email_template'                     => 'contacts_email_email_template',
    'currency/import/error_email_template'              => 'currency_import_error_email_template',
    'customer/create_account/email_template'            => 'customer_create_account_email_template',
    'customer/password_forgot/email_template'           => 'customer_password_forgot_email_template',
    'newsletter/subscription/confirm_email_template'    => 'newsletter_subscription_confirm_email_template',
    'newsletter/subscription/success_email_template'    => 'newsletter_subscription_success_email_template',
    'newsletter/subscription/un_email_template'         => 'newsletter_subscription_un_email_template',
    'sales_email/order/template'                        => 'sales_email_order_template',
    'sales_email/order_comment/template'                => 'sales_email_order_comment_template',
    'sales_email/invoice/template'                      => 'sales_email_invoice_comment_template',
    'sales_email/invoice_comment/template'              => 'sales_email_invoice_comment_template',
    'sales_email/creditmemo/template'                   => 'sales_email_creditmemo_template',
    'sales_email/creditmemo_comment/template'           => 'sales_email_creditmemo_comment_template',
    'sales_email/shipment/template'                     => 'sales_email_shipment_template',
    'sales_email/shipment_comment/template'             => 'sales_email_shipment_comment_template',
    'sendfriend/email/template'                         => 'sendfriend_email_template',
    'sitemap/generate/error_email_template'             => 'sitemap_generate_error_email_template',
    'wishlist/email/email_template'                     => 'wishlist_email_email_template',
);

foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}
