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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$orderGridTable             = $installer->getTable('sales/order_grid');
$orderTable                 = $installer->getTable('sales/order');
$paymentTransactionTable    = $installer->getTable('sales/payment_transaction');
$profileTable               = $installer->getTable('sales_recurring_profile');
$orderItemTable             = $installer->getTable('sales_flat_order_item');
$flatOrderTable             = $installer->getTable('sales_flat_order');
$profileOrderTable          = $installer->getTable('sales_recurring_profile_order');
$customerEntityTable        = $installer->getTable('customer_entity');
$coreStoreTable             = $installer->getTable('core_store');
$billingAgreementTable      = $installer->getTable('sales/billing_agreement');
$billingAgreementOrderTable = $installer->getTable('sales/billing_agreement_order');

//-------
$installer->getConnection()->addColumn($orderGridTable,
    'store_name', 'varchar(255) null default null AFTER `store_id`');

$installer->run("
    UPDATE {$orderGridTable} AS og
        INNER JOIN  {$orderTable} AS o on (og.entity_id=o.entity_id)
    SET
        og.store_name = o.store_name
");

//-------
$installer->getConnection()->addColumn($paymentTransactionTable,
    'created_at', 'DATETIME NULL');

//-------
$this->getConnection()->addColumn($orderItemTable, 'is_nominal', 'int NOT NULL DEFAULT \'0\'');

//-------
$installer->run("
    CREATE TABLE `{$billingAgreementTable}` (
      `agreement_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `customer_id` int(10) unsigned NOT NULL,
      `method_code` varchar(32) NOT NULL,
      `reference_id` varchar(32) NOT NULL,
      `status` varchar(20) NOT NULL,
      `created_at` datetime NOT NULL,
      `updated_at` datetime DEFAULT NULL,
      PRIMARY KEY (`agreement_id`),
      KEY `IDX_CUSTOMER` (`customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_BILLING_AGREEMENT_CUSTOMER',
    $billingAgreementTable,
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'

);

//-------
$installer->run("
    CREATE TABLE `{$billingAgreementOrderTable}` (
      `agreement_id` int(10) unsigned NOT NULL,
      `order_id` int(10) unsigned NOT NULL,
      UNIQUE KEY `UNQ_BILLING_AGREEMENT_ORDER` (`agreement_id`,`order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_BILLING_AGREEMENT_ORDER_AGREEMENT',
    $billingAgreementOrderTable,
    'agreement_id',
    $billingAgreementTable,
    'agreement_id'
);

$installer->getConnection()->addConstraint(
    'FK_BILLING_AGREEMENT_ORDER_ORDER',
    $billingAgreementOrderTable,
    'order_id',
    $orderTable,
    'entity_id'
);

//-------

$this->run("
CREATE TABLE `{$profileTable}` (
  `profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(20) NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `method_code` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `reference_id` varchar(32) DEFAULT NULL,
  `subscriber_name` varchar(150) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `internal_reference_id` varchar(42) NOT NULL,
  `schedule_description` varchar(255) NOT NULL,
  `suspension_threshold` smallint(6) unsigned DEFAULT NULL,
  `bill_failed_later` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `period_unit` varchar(20) NOT NULL,
  `period_frequency` tinyint(3) unsigned DEFAULT NULL,
  `period_max_cycles` tinyint(3) unsigned DEFAULT NULL,
  `billing_amount` double(12,4) unsigned NOT NULL DEFAULT '0.0000',
  `trial_period_unit` varchar(20) DEFAULT NULL,
  `trial_period_frequency` tinyint(3) unsigned DEFAULT NULL,
  `trial_period_max_cycles` tinyint(3) unsigned DEFAULT NULL,
  `trial_billing_amount` double(12,4) unsigned DEFAULT NULL,
  `currency_code` char(3) NOT NULL,
  `shipping_amount` decimal(12,4) unsigned DEFAULT NULL,
  `tax_amount` decimal(12,4) unsigned DEFAULT NULL,
  `init_amount` decimal(12,4) unsigned DEFAULT NULL,
  `init_may_fail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order_info` text NOT NULL,
  `order_item_info` text NOT NULL,
  `billing_address_info` text NOT NULL,
  `shipping_address_info` text DEFAULT NULL,
  `profile_vendor_info` text DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `UNQ_INTERNAL_REF_ID` (`internal_reference_id`),
  KEY `IDX_RECURRING_PROFILE_CUSTOMER` (`customer_id`),
  KEY `IDX_RECURRING_PROFILE_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_CUSTOMER', $profileTable, 'customer_id',
    $customerEntityTable, 'entity_id', 'SET NULL'
);

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_STORE', $profileTable, 'store_id',
    $coreStoreTable, 'store_id', 'SET NULL'
);

$this->run("
CREATE TABLE `{$profileOrderTable}` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`link_id`),
  UNIQUE KEY `UNQ_PROFILE_ORDER` (`profile_id`,`order_id`),
  KEY `IDX_ORDER` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_ORDER_PROFILE', $profileOrderTable, 'profile_id',
    $profileTable, 'profile_id'
);

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_ORDER_ORDER', $profileOrderTable, 'order_id',
    $flatOrderTable, 'entity_id'
);
