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


/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;

$tablePaymentTransaction = $this->getTable('sales/payment_transaction');
$tableOrders = $this->getTable('sales/order');
$tableOrderPayment = $this->getTable('sales/order_entity');

$installer->run("
CREATE TABLE `{$tablePaymentTransaction}` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `payment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `txn_id` varchar(100) NOT NULL DEFAULT '',
  `parent_txn_id` varchar(100) DEFAULT NULL,
  `txn_type` varchar(15) NOT NULL DEFAULT '',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `additional_information` blob,
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `UNQ_SALES_TXN_ORDER_PAYMENT_TXN` (`order_id`,`payment_id`,`txn_id`),
  KEY `FK_SALES_TXN_PARENT` (`parent_id`),
  KEY `FK_SALES_TXN_PAYMENT` (`payment_id`),
  CONSTRAINT `FK_SALES_PAYMENT_TXN_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `{$tablePaymentTransaction}` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_TXN_ORDER` FOREIGN KEY (`order_id`) REFERENCES `{$tableOrders}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SALES_TXN_PAYMENT` FOREIGN KEY (`payment_id`) REFERENCES `{$tableOrderPayment}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);
