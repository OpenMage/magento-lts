<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

$installer->run("
CREATE TABLE `{$installer->getTable('customer/form_attribute')}` (
  `form_code` char(32) NOT NULL,
  `attribute_id` smallint UNSIGNED NOT NULL,
  PRIMARY KEY(`form_code`, `attribute_id`),
  KEY `IDX_CUSTOMER_FORM_ATTRIBUTE_ATTRIBUTE` (`attribute_id`),
  CONSTRAINT `FK_CUSTOMER_FORM_ATTRIBUTE_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer attributes/forms relations';
");

$installer->getConnection()->dropColumn($installer->getTable('customer/eav_attribute'), 'is_visible_on_front');
$installer->getConnection()->changeColumn(
    $installer->getTable('customer/eav_attribute'),
    'lines_to_divide_multiline',
    'multiline_count',
    'TINYINT UNSIGNED NOT NULL DEFAULT 1',
);
$installer->getConnection()->dropColumn($installer->getTable('customer/eav_attribute'), 'min_text_length');
$installer->getConnection()->dropColumn($installer->getTable('customer/eav_attribute'), 'max_text_length');
$installer->getConnection()->modifyColumn(
    $installer->getTable('customer/eav_attribute'),
    'input_filter',
    'varchar(255) DEFAULT NULL',
);
$installer->getConnection()->addColumn(
    $installer->getTable('customer/eav_attribute'),
    'validate_rules',
    'text DEFAULT NULL',
);
$installer->getConnection()->addColumn(
    $installer->getTable('customer/eav_attribute'),
    'is_system',
    'TINYINT UNSIGNED NOT NULL DEFAULT 0',
);
$installer->getConnection()->addColumn(
    $installer->getTable('customer/eav_attribute'),
    'sort_order',
    'INT UNSIGNED NOT NULL DEFAULT 0',
);

$installer->updateEntityType('customer', 'attribute_model', 'customer/attribute');
$installer->updateEntityType('customer_address', 'attribute_model', 'customer/attribute');
