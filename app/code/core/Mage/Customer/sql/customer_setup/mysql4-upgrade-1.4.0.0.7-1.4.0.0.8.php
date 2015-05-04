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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Customer_Model_Entity_Setup */
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
$installer->getConnection()->changeColumn($installer->getTable('customer/eav_attribute'), 'lines_to_divide_multiline',
    'multiline_count', 'TINYINT UNSIGNED NOT NULL DEFAULT 1');
$installer->getConnection()->dropColumn($installer->getTable('customer/eav_attribute'), 'min_text_length');
$installer->getConnection()->dropColumn($installer->getTable('customer/eav_attribute'), 'max_text_length');
$installer->getConnection()->modifyColumn($installer->getTable('customer/eav_attribute'), 'input_filter',
    'varchar(255) DEFAULT NULL');
$installer->getConnection()->addColumn($installer->getTable('customer/eav_attribute'), 'validate_rules',
    'text DEFAULT NULL');
$installer->getConnection()->addColumn($installer->getTable('customer/eav_attribute'), 'is_system',
    'TINYINT UNSIGNED NOT NULL DEFAULT 0');
$installer->getConnection()->addColumn($installer->getTable('customer/eav_attribute'), 'sort_order',
    'INT UNSIGNED NOT NULL DEFAULT 0');

$installer->updateEntityType('customer', 'attribute_model', 'customer/attribute');
$installer->updateEntityType('customer_address', 'attribute_model', 'customer/attribute');
