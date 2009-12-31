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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$this->startSetup();
$this->run("
ALTER TABLE `{$installer->getTable('sales_quote')}`
    change `is_active` `is_active` tinyint (1)UNSIGNED  DEFAULT '1' NULL ,
    change `is_virtual` `is_virtual` tinyint (1)UNSIGNED  DEFAULT '0' NULL ,
    change `is_multi_shipping` `is_multi_shipping` tinyint (1)UNSIGNED  DEFAULT '0' NULL ,
    change `is_multi_payment` `is_multi_payment` tinyint (1)UNSIGNED  DEFAULT '0' NULL ,
    change `customer_note_notify` `customer_note_notify` tinyint (1)UNSIGNED  DEFAULT '1' NULL ,
    change `customer_is_guest` `customer_is_guest` tinyint (1)UNSIGNED  DEFAULT '0' NULL ,
    change `quote_status_id` `quote_status_id` int (10)UNSIGNED  DEFAULT '0' NULL ,
    change `billing_address_id` `billing_address_id` int (10)UNSIGNED  DEFAULT '0' NULL ,
    change `orig_order_id` `orig_order_id` int (10)UNSIGNED  DEFAULT '0' NULL ,
    change `customer_id` `customer_id` int (10)UNSIGNED  DEFAULT '0' NULL ,
    change `customer_tax_class_id` `customer_tax_class_id` int (10)UNSIGNED  DEFAULT '0' NULL ,
    change `customer_group_id` `customer_group_id` int (10)UNSIGNED  DEFAULT '0' NULL ,
    change `items_count` `items_count` int (10)UNSIGNED  DEFAULT '0' NULL ,
    change `items_qty` `items_qty` decimal (12,4) DEFAULT '0.0000' NULL ,
    change `store_to_base_rate` `store_to_base_rate` decimal (12,4) DEFAULT '0.0000' NULL ,
    change `store_to_quote_rate` `store_to_quote_rate` decimal (12,4) DEFAULT '0.0000' NULL ,
    change `grand_total` `grand_total` decimal (12,4) DEFAULT '0.0000' NULL ,
    change `base_grand_total` `base_grand_total` decimal (12,4) DEFAULT '0.0000' NULL ,
    change `custbalance_amount` `custbalance_amount` decimal (12,4) DEFAULT '0.0000' NULL ,
    change `checkout_method` `checkout_method` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `password_hash` `password_hash` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `coupon_code` `coupon_code` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `base_currency_code` `base_currency_code` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `store_currency_code` `store_currency_code` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `quote_currency_code` `quote_currency_code` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `customer_email` `customer_email` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `customer_firstname` `customer_firstname` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `customer_lastname` `customer_lastname` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `customer_note` `customer_note` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `remote_ip` `remote_ip` varchar (255)  NULL  COLLATE utf8_general_ci ,
    change `applied_rule_ids` `applied_rule_ids` varchar (255)  NULL  COLLATE utf8_general_ci
");
$this->endSetup();
$this->installEntities();
