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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->run("
CREATE TABLE `{$installer->getTable('sales_flat_order_item')}` (
    `item_id` int(10) unsigned NOT NULL auto_increment,
    `order_id` int(10) unsigned NOT NULL default '0',
    `parent_item_id` int(10) unsigned default NULL,
    `quote_item_id` int(10) unsigned default NULL,
    `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
    `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',

    `product_id` int(10) unsigned default NULL,
    `product_type` varchar(255) default NULL,
    `product_options` text default NULL,
    `weight` decimal(12,4) default '0.0000',
    `is_virtual` tinyint(1) unsigned default NULL,
    `sku` varchar(255) NOT NULL default '',
    `name` varchar(255) default NULL,
    `description` text,
    `applied_rule_ids` text,
    `additional_data` text,

    `free_shipping` tinyint(1) unsigned NOT NULL default '0',
    `is_qty_decimal` tinyint(1) unsigned default NULL,
    `no_discount` tinyint(1) unsigned default '0',

    `qty_backordered` decimal(12,4) default '0.0000',
    `qty_canceled` decimal(12,4) default '0.0000',
    `qty_invoiced` decimal(12,4) default '0.0000',
    `qty_ordered` decimal(12,4) default '0.0000',
    `qty_refunded` decimal(12,4) default '0.0000',
    `qty_shipped` decimal(12,4) default '0.0000',

    `cost` decimal(12,4) default '0.0000',
    `price` decimal(12,4) NOT NULL default '0.0000',
    `base_price` decimal(12,4) NOT NULL default '0.0000',
    `original_price` decimal(12,4) default NULL,
    `base_original_price` decimal(12,4) default NULL,

    `tax_percent` decimal(12,4) default '0.0000',
    `tax_amount` decimal(12,4) default '0.0000',
    `base_tax_amount` decimal(12,4) default '0.0000',
    `tax_invoiced` decimal(12,4) default '0.0000',
    `base_tax_invoiced` decimal(12,4) default '0.0000',

    `discount_percent` decimal(12,4) default '0.0000',
    `discount_amount` decimal(12,4) default '0.0000',
    `base_discount_amount` decimal(12,4) default '0.0000',
    `discount_invoiced` decimal(12,4) default '0.0000',
    `base_discount_invoiced` decimal(12,4) default '0.0000',

    `amount_refunded` decimal(12,4) default '0.0000',
    `base_amount_refunded` decimal(12,4) default '0.0000',

    `row_total` decimal(12,4) NOT NULL default '0.0000',
    `base_row_total` decimal(12,4) NOT NULL default '0.0000',
    `row_invoiced` decimal(12,4) NOT NULL default '0.0000',
    `base_row_invoiced` decimal(12,4) NOT NULL default '0.0000',

    `row_weight` decimal(12,4) default '0.0000',

    `gift_message_id` int(10) default NULL,
    `gift_message_available` int(10) default NULL,

    PRIMARY KEY  (`item_id`),
    KEY `IDX_ORDER` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$itemFields     = $installer->getConnection()->describeTable($installer->getTable('sales_flat_order_item'));
$itemEntityId   = $installer->getEntityTypeId('order_item');

$itemAttributes = $installer->getConnection()->fetchPairs("SELECT attribute_id, attribute_code FROM {$installer->getTable('eav_attribute')} WHERE entity_type_id={$itemEntityId}");

$items = $installer->getConnection()->fetchAll("SELECT * FROM {$installer->getTable('sales_order_entity')} WHERE entity_type_id={$itemEntityId}");

foreach ($items as $itemData) {
    $itemId = $itemData['entity_id'];
    $sql = "
    SELECT
        attribute_id, value
    FROM {$installer->getTable('sales_order_entity_decimal')}
    WHERE
        entity_id={$itemId}
        AND entity_type_id={$itemEntityId}

    UNION
    SELECT
        attribute_id, value
    FROM {$installer->getTable('sales_order_entity_datetime')}
    WHERE
        entity_id={$itemId}
        AND entity_type_id={$itemEntityId}

    UNION

    SELECT
        attribute_id, value
    FROM {$installer->getTable('sales_order_entity_int')}
    WHERE
        entity_id={$itemId}
        AND entity_type_id={$itemEntityId}

    UNION

    SELECT
        attribute_id, value
    FROM {$installer->getTable('sales_order_entity_text')}
    WHERE
        entity_id={$itemId}
        AND entity_type_id={$itemEntityId}

    UNION

    SELECT
        attribute_id, value
    FROM {$installer->getTable('sales_order_entity_varchar')}
    WHERE
        entity_id={$itemId}
        AND entity_type_id={$itemEntityId}
    ";

    $data = $installer->getConnection()->fetchPairs($sql);
    foreach ($data as $attributeId => $attributeValue) {
        if (isset($itemAttributes[$attributeId])) {
            $itemData[$itemAttributes[$attributeId]] = $attributeValue;
        }
    }

    $newItemData = array(
        'item_id'   => $itemData['entity_id'],
        'order_id'  => $itemData['parent_id'],
    );

    foreach ($itemData as $key => $value) {
        if (isset($itemFields[$key])) {
            $newItemData[$key] = $value;
        }
    }

    $installer->getConnection()->insert($installer->getTable('sales_flat_order_item'), $newItemData);
}
