<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("

/*Table structure for table `sales_order` */

DROP TABLE IF EXISTS {$this->getTable('sales_order')};
CREATE TABLE {$this->getTable('sales_order')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `increment_id` varchar(50) NOT NULL default '',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  `customer_id` int(11),
  `tax_amount` decimal(12,4) NOT NULL default '0.0000',
  `shipping_amount` decimal(12,4) NOT NULL default '0.0000',
  `discount_amount` decimal(12,4) NOT NULL default '0.0000',
  `subtotal` decimal(12,4) NOT NULL default '0.0000',
  `grand_total` decimal(12,4) NOT NULL default '0.0000',
  `total_paid` decimal(12,4) NOT NULL default '0.0000',
  `total_refunded` decimal(12,4) NOT NULL default '0.0000',
  `total_qty_ordered` decimal(12,4) NOT NULL default '0.0000',
  `total_canceled` decimal(12,4) NOT NULL default '0.0000',
  `total_invoiced` decimal(12,4) NOT NULL default '0.0000',
  `total_online_refunded` decimal(12,4) NOT NULL default '0.0000',
  `total_offline_refunded` decimal(12,4) NOT NULL default '0.0000',
  `base_tax_amount` decimal(12,4) NOT NULL default '0.0000',
  `base_shipping_amount` decimal(12,4) NOT NULL default '0.0000',
  `base_discount_amount` decimal(12,4) NOT NULL default '0.0000',
  `base_subtotal` decimal(12,4) NOT NULL default '0.0000',
  `base_grand_total` decimal(12,4) NOT NULL default '0.0000',
  `base_total_paid` decimal(12,4) NOT NULL default '0.0000',
  `base_total_refunded` decimal(12,4) NOT NULL default '0.0000',
  `base_total_qty_ordered` decimal(12,4) NOT NULL default '0.0000',
  `base_total_canceled` decimal(12,4) NOT NULL default '0.0000',
  `base_total_invoiced` decimal(12,4) NOT NULL default '0.0000',
  `base_total_online_refunded` decimal(12,4) NOT NULL default '0.0000',
  `base_total_offline_refunded` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_sales_order_type` (`entity_type_id`),
  KEY `FK_sales_order_store` (`store_id`),
  CONSTRAINT `FK_SALE_ORDER_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_SALE_ORDER_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


DROP TABLE IF EXISTS {$this->getTable('sales_order')}_datetime;
CREATE TABLE `{$this->getTable('sales_order')}_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_datetime_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_datetime_attribute` (`attribute_id`),
  KEY `FK_sales_order_datetime` (`entity_id`),
  CONSTRAINT `FK_sales_order_datetime` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_datetime_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_datetime_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('sales_order')}_decimal;
CREATE TABLE `{$this->getTable('sales_order')}_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_decimal_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_decimal_attribute` (`attribute_id`),
  KEY `FK_sales_order_decimal` (`entity_id`),
  CONSTRAINT `FK_sales_order_decimal` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_decimal_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_decimal_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('sales_order')}_int;
CREATE TABLE `{$this->getTable('sales_order')}_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_int_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_int_attribute` (`attribute_id`),
  KEY `FK_sales_order_int` (`entity_id`),
  CONSTRAINT `FK_sales_order_int` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_int_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_int_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('sales_order')}_text;
CREATE TABLE `{$this->getTable('sales_order')}_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_text_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_text_attribute` (`attribute_id`),
  KEY `FK_sales_order_text` (`entity_id`),
  CONSTRAINT `FK_sales_order_text` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_text_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_text_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('sales_order')}_varchar;
CREATE TABLE `{$this->getTable('sales_order')}_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_sales_order_varchar_entity_type` (`entity_type_id`),
  KEY `FK_sales_order_varchar_attribute` (`attribute_id`),
  KEY `FK_sales_order_varchar` (`entity_id`),
  CONSTRAINT `FK_sales_order_varchar` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_varchar_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sales_order_varchar_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

$orderEntityTypeId = $this->getEntityTypeId('order');

$attributes = [
    'customer_id' => [],
    'tax_amount' => [],
    'shipping_amount' => [],
    'discount_amount' => [],
    'subtotal' => [],
    'grand_total' => [],
    'total_paid' => [],
    'total_refunded' => [],
    'total_qty_ordered' => [],
    'total_canceled' => [],
    'total_invoiced' => [],
    'total_online_refunded' => [],
    'total_offline_refunded' => [],
    'base_tax_amount' => [],
    'base_shipping_amount' => [],
    'base_discount_amount' => [],
    'base_subtotal' => [],
    'base_grand_total' => [],
    'base_total_paid' => [],
    'base_total_refunded' => [],
    'base_total_qty_ordered' => [],
    'base_total_canceled' => [],
    'base_total_invoiced' => [],
    'base_total_online_refunded' => [],
    'base_total_offline_refunded' => [],
];

$select = new Zend_Db_Select($installer->getConnection());
$select->from(['e' => $this->getTable('sales_order_entity')]);

$attributeIds = [];
foreach (array_keys($attributes) as $code) {
    $attributes[$code] = $installer->getAttribute($orderEntityTypeId, $code);
    if ($attributes[$code]['backend_type'] != 'static') {
        $select->joinLeft(
            ["_table_{$code}" => "{$this->getTable('sales_order_entity')}_{$attributes[$code]['backend_type']}"],
            "_table_{$code}.attribute_id = {$attributes[$code]['attribute_id']} AND _table_{$code}.entity_id = e.entity_id",
            [$code => 'value'],
        );
        $select->join(
            ["_eav_atr_{$code}" => $this->getTable('eav/attribute')],
            "_eav_atr_{$code}.attribute_id = {$attributes[$code]['attribute_id']}",
            [],
        );
        $attributeIds[] = $attributes[$code]['attribute_id'];
    }
}

$select->where("e.entity_type_id = {$orderEntityTypeId}");

$orders = $installer->getConnection()->fetchAll($select);

foreach ($orders as $order) {
    $old_entity_id = $order['entity_id'];
    unset($order['entity_id']);
    unset($order['parent_id']);
    foreach ($order as $key => $field) {
        if ($field == '') {
            unset($order[$key]);
        }
    }
    $installer->getConnection()->insert($this->getTable('sales_order'), $order);
    $new_entity_id = $installer->getConnection()->lastInsertId();

    $installer->run("UPDATE {$this->getTable('sales_order_entity')} SET parent_id={$new_entity_id} WHERE parent_id={$old_entity_id}");

    $tables = ['varchar', 'int', 'datetime', 'text', 'decimal'];
    foreach ($tables as $table) {
        $delete = [];
        $attrs = $installer->getConnection()->fetchAll("SELECT tt.* FROM {$this->getTable('sales_order_entity')}_{$table} tt JOIN eav_attribute on eav_attribute.attribute_id = tt.attribute_id  WHERE entity_id={$old_entity_id}");
        foreach ($attrs as $attr) {
            if (!in_array($attr['attribute_id'], $attributeIds)) {
                unset($attr['value_id']);
                $attr['entity_id'] = $new_entity_id;
                $installer->getConnection()->insert("{$this->getTable('sales_order')}_{$table}", $attr);
            }
        }
    }
}

$installer->run("DELETE FROM {$this->getTable('sales_order_entity')} WHERE parent_id=0");

$installer->installEntities();
