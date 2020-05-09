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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer->startSetup();
$installer->run("
CREATE TABLE `{$installer->getTable('sales_flat_quote')}` (
    `entity_id` int(10) unsigned NOT NULL auto_increment,
    `store_id` smallint(5) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `converted_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `is_active` tinyint(1) unsigned default '1',
    `is_virtual` tinyint(1) unsigned default '0',
    `is_multi_shipping` tinyint(1) unsigned default '0',

    `items_count` int(10) unsigned default '0',
    `items_qty` decimal(12,4) default '0.0000',

    `orig_order_id` int(10) unsigned default '0',

    `store_to_base_rate` decimal(12,4) default '0.0000',
    `store_to_quote_rate` decimal(12,4) default '0.0000',
    `base_currency_code` varchar(255) default NULL,
    `store_currency_code` varchar(255) default NULL,
    `quote_currency_code` varchar(255) default NULL,

    `grand_total` decimal(12,4) default '0.0000',
    `base_grand_total` decimal(12,4) default '0.0000',

    `checkout_method` varchar(255) default NULL,

    `customer_id` int(10) unsigned default '0',
    `customer_tax_class_id` int(10) unsigned default '0',
    `customer_group_id` int(10) unsigned default '0',
    `customer_email` varchar(255) default NULL,
    `customer_prefix` varchar(40) default NULL,
    `customer_firstname` varchar(255) default NULL,
    `customer_middlename` varchar(40) default NULL,
    `customer_lastname` varchar(255) default NULL,
    `customer_suffix` varchar(40) default NULL,
    `customer_dob` datetime default NULL,
    `customer_note` varchar(255) default NULL,
    `customer_note_notify` tinyint(1) unsigned default '1',
    `customer_is_guest` tinyint(1) unsigned default '0',

    `remote_ip` varchar(32) default NULL,
    `applied_rule_ids` varchar(255) default NULL,
    `reserved_order_id` varchar(64) default '',
    `password_hash` varchar(255) default NULL,
    `coupon_code` varchar(255) default NULL,
    PRIMARY KEY  (`entity_id`),
    KEY `FK_SALES_QUOTE_STORE` (`store_id`),
    KEY `IDX_CUSTOMER` (`customer_id`,`store_id`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `{$installer->getTable('sales_flat_quote_address')}` (
    `address_id` int(10) unsigned NOT NULL auto_increment,
    `quote_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `customer_id` int(10) unsigned default NULL,
    `save_in_address_book` tinyint(1) default '0',
    `customer_address_id` int(10) unsigned default NULL,
    `address_type` varchar(255) default NULL,
    `email` varchar(255) default NULL,
    `prefix` varchar(40) default NULL,
    `firstname` varchar(255) default NULL,
    `middlename` varchar(40) default NULL,
    `lastname` varchar(255) default NULL,
    `suffix` varchar(40) default NULL,
    `company` varchar(255) default NULL,
    `street` varchar(255) default NULL,
    `city` varchar(255) default NULL,
    `region` varchar(255) default NULL,
    `region_id` int(10) unsigned default NULL,
    `postcode` varchar(255) default NULL,
    `country_id` varchar(255) default NULL,
    `telephone` varchar(255) default NULL,
    `fax` varchar(255) default NULL,

    `same_as_billing` tinyint(1) unsigned NOT NULL default '0',
    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `collect_shipping_rates` tinyint(1) unsigned NOT NULL default '0',
    `shipping_method` varchar(255) NOT NULL default '',
    `shipping_description` varchar(255) NOT NULL default '',
    `weight` decimal(12,4) NOT NULL default '0.0000',

    `subtotal` decimal(12,4) NOT NULL default '0.0000',
    `base_subtotal` decimal(12,4) NOT NULL default '0.0000',
    `subtotal_with_discount` decimal(12,4) NOT NULL default '0.0000',
    `base_subtotal_with_discount` decimal(12,4) NOT NULL default '0.0000',
    `tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_tax_amount` decimal(12,4) NOT NULL default '0.0000',
    `shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_shipping_amount` decimal(12,4) NOT NULL default '0.0000',
    `shipping_tax_amount` decimal(12,4) default NULL,
    `base_shipping_tax_amount` decimal(12,4) default NULL,
    `discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `base_discount_amount` decimal(12,4) NOT NULL default '0.0000',
    `grand_total` decimal(12,4) NOT NULL default '0.0000',
    `base_grand_total` decimal(12,4) NOT NULL default '0.0000',

    `customer_notes` text,
    PRIMARY KEY  (`address_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_SALES_QUOTE` (`quote_id`),
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_SALES_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_address_item')}` (
    `address_item_id` int(10) unsigned NOT NULL auto_increment,
    `quote_address_id` int(10) unsigned NOT NULL default '0',
    `quote_item_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `applied_rule_ids` text,
    `additional_data` text,
    `weight` decimal(12,4) default '0.0000',
    `qty` decimal(12,4) NOT NULL default '0.0000',
    `discount_amount` decimal(12,4) default '0.0000',
    `tax_amount` decimal(12,4) default '0.0000',

    `row_total` decimal(12,4) NOT NULL default '0.0000',
    `base_row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_total_with_discount` decimal(12,4) default '0.0000',
    `base_discount_amount` decimal(12,4) default '0.0000',
    `base_tax_amount` decimal(12,4) default '0.0000',
    `row_weight` decimal(12,4) default '0.0000',
    PRIMARY KEY  (`address_item_id`),
    KEY `FK_QUOTE_ADDRESS_ITEM_QUOTE_ADDRESS` (`quote_address_id`),
    KEY `FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM` (`quote_item_id`),
    CONSTRAINT `FK_QUOTE_ADDRESS_ITEM_QUOTE_ADDRESS` FOREIGN KEY (`quote_address_id`) REFERENCES `{$installer->getTable('sales_flat_quote_address')}` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SALES_QUOTE_ADDRESS_ITEM_QUOTE_ITEM` FOREIGN KEY (`quote_item_id`) REFERENCES `{$installer->getTable('sales_flat_quote_item')}` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_item')}` (
    `item_id` int(10) unsigned NOT NULL auto_increment,
    `quote_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `product_id` int(10) unsigned default NULL,
    `super_product_id` int(10) unsigned default NULL,
    `parent_product_id` int(10) unsigned default NULL,
    `is_virtual` tinyint(1) unsigned default NULL,

    `sku` varchar(255) NOT NULL default '',
    `name` varchar(255) default NULL,
    `description` text,
    `applied_rule_ids` text,
    `additional_data` text,
    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `is_qty_decimal` tinyint(1) unsigned default NULL,
    `no_discount` tinyint(1) unsigned default '0',

    `weight` decimal(12,4) default '0.0000',
    `qty` decimal(12,4) NOT NULL default '0.0000',
    `price` decimal(12,4) NOT NULL default '0.0000',
    `base_price` decimal(12,4) NOT NULL default '0.0000',
    `custom_price` decimal(12,4) default NULL,
    `discount_percent` decimal(12,4) default '0.0000',
    `discount_amount` decimal(12,4) default '0.0000',
    `base_discount_amount` decimal(12,4) default '0.0000',
    `tax_percent` decimal(12,4) default '0.0000',
    `tax_amount` decimal(12,4) default '0.0000',
    `base_tax_amount` decimal(12,4) default '0.0000',
    `row_total` decimal(12,4) NOT NULL default '0.0000',
    `base_row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_total_with_discount` decimal(12,4) default '0.0000',
    `row_weight` decimal(12,4) default '0.0000',
    PRIMARY KEY  (`item_id`),
    KEY `FK_SALES_QUOTE_ITEM_SALES_QUOTE` (`quote_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_SALES_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_item_option')}` (
    `option_id` int(10) unsigned NOT NULL auto_increment,
    `item_id` int(10) unsigned NOT NULL,
    `product_id` int(10) unsigned NOT NULL,
    `code` varchar(255) NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY  (`option_id`),
    KEY `FK_SALES_QUOTE_ITEM_OPTION_ITEM_ID` (`item_id`),
    CONSTRAINT `FK_SALES_QUOTE_ITEM_OPTION_ITEM_ID` FOREIGN KEY (`item_id`) REFERENCES `{$installer->getTable('sales_flat_quote_item')}` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Additional options for quote item';

CREATE TABLE `{$installer->getTable('sales_flat_quote_payment')}` (
    `payment_id` int(10) unsigned NOT NULL auto_increment,
    `quote_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `method` varchar(255) default '',

    `cc_type` varchar(255) default '',
    `cc_number_enc` varchar(255) default '',
    `cc_last4` varchar(255) default '',
    `cc_cid_enc` varchar(255) default '',
    `cc_owner` varchar(255) default '',
    `cc_exp_month` tinyint(2) unsigned default '0',
    `cc_exp_year` smallint(4) unsigned default '0',
    `cc_ss_owner` varchar(255) default '',
    `cc_ss_start_month` tinyint(2) unsigned default '0',
    `cc_ss_start_year` smallint(4) unsigned default '0',

    `cybersource_token` varchar(255) default '',
    `paypal_correlation_id` varchar(255) default '',
    `paypal_payer_id` varchar(255) default '',
    `paypal_payer_status` varchar(255) default '',
    `po_number` varchar(255) default '',
    PRIMARY KEY  (`payment_id`),
    KEY `FK_SALES_QUOTE_PAYMENT_SALES_QUOTE` (`quote_id`),
    CONSTRAINT `FK_SALES_QUOTE_PAYMENT_SALES_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$installer->getTable('sales_flat_quote')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('sales_flat_quote_shipping_rate')}` (
    `rate_id` int(10) unsigned NOT NULL auto_increment,
    `address_id` int(10) unsigned NOT NULL default '0',
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `carrier` varchar(255) default NULL,
    `carrier_title` varchar(255) default NULL,
    `code` varchar(255) default NULL,
    `method` varchar(255) default NULL,
    `method_description` text,
    `price` decimal(12,4) NOT NULL default '0.0000',
    PRIMARY KEY  (`rate_id`),
    KEY `FK_SALES_QUOTE_SHIPPING_RATE_ADDRESS` (`address_id`),
    CONSTRAINT `FK_SALES_QUOTE_SHIPPING_RATE_ADDRESS` FOREIGN KEY (`address_id`) REFERENCES `{$installer->getTable('sales_flat_quote_address')}` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELETE FROM `{$installer->getTable('log_quote')}`;
");

$installer->addAttribute('order_item', 'is_virtual', array('type'=>'int'));
$installer->endSetup();

/**
 * Copy old quotes
 */
@set_time_limit(0);

$quoteFields = array_keys($installer->getConnection()->describeTable($installer->getTable('sales_flat_quote')));
$itemFields  = array_keys($installer->getConnection()->describeTable($installer->getTable('sales_flat_quote_item')));

$quoteRows = array();
$query = $installer->getConnection()->query(
    $installer->getConnection()->select()
        ->from($installer->getTable('sales_quote'), 'entity_id')
);
while ($row = $query->fetch()) {
    $quoteRows[] = $row['entity_id'];
}

foreach ($quoteRows as $oldQuoteId) {
    $quoteInfo = $installer->getConnection()->fetchRow(
        $installer->getConnection()->select()
            ->from($installer->getTable('sales_quote'))
            ->where('entity_id=?', $oldQuoteId)
    );

    $quoteItems = $installer->getConnection()->fetchAll(
        $installer->getConnection()->select()
            ->from($installer->getTable('sales_quote_item'))
            ->where('parent_id=?', $oldQuoteId)
    );

    if (!empty($quoteItems)) {
        unset($quoteInfo['entity_id']);

        $quoteData = array();
        foreach ($quoteFields as $field) {
            if (isset($quoteInfo[$field])) {
                $quoteData[$field] = $quoteInfo[$field];
            }
        }

        $installer->getConnection()->insert($installer->getTable('sales_flat_quote'), $quoteData);
        $quoteId = $installer->getConnection()->lastInsertId();

        foreach ($quoteItems as $itemInfo) {
            $itemData = array(
                'quote_id' => $quoteId
            );

            foreach ($itemFields as $field) {
                if (isset($itemInfo[$field])) {
                    $itemData[$field] = $itemInfo[$field];
                }
            }
            $installer->getConnection()->insert($installer->getTable('sales_flat_quote_item'), $itemData);
        }
    }
}

$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('sales_quote')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_address')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_address_decimal')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_address_int')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_address_text')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_address_varchar')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_decimal')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_datetime')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_decimal')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_int')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_text')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_entity_varchar')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_int')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_item')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_item_decimal')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_item_int')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_item_text')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_item_varchar')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_text')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_varchar')};
DROP TABLE IF EXISTS {$this->getTable('sales_quote_rule')};


DROP TABLE IF EXISTS {$this->getTable('sales_counter')};
DROP TABLE IF EXISTS {$this->getTable('sales_discount_coupon')};
");
$installer->endSetup();
