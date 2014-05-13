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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();

$installer->updateEntityType('customer', 'additional_attribute_table', 'customer/eav_attribute');
$installer->updateEntityType('customer', 'entity_attribute_collection', 'customer/attribute_collection');
$installer->updateEntityType('customer_address', 'additional_attribute_table', 'customer/eav_attribute');
$installer->updateEntityType('customer_address', 'entity_attribute_collection', 'customer/address_attribute_collection');
$installer->run("
CREATE TABLE `{$installer->getTable('customer/eav_attribute')}` (
  `attribute_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `is_visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_visible_on_front` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `input_filter` varchar(255) NOT NULL,
  `lines_to_divide_multiline` smallint(5) unsigned NOT NULL DEFAULT '0',
  `min_text_length` int(11) unsigned NOT NULL DEFAULT '0',
  `max_text_length` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attribute_id`),
  CONSTRAINT `FK_CUSTOMER_EAV_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$visibleAttributes = array('store_id', 'default_billing', 'default_shipping', 'confirmation');
$stmt = $installer->getConnection()->select()
    ->from($installer->getTable('eav/attribute'), array('attribute_id', 'attribute_code'))
    ->where('entity_type_id = ?', $installer->getEntityTypeId('customer'))
    ->orWhere('entity_type_id = ?', $installer->getEntityTypeId('customer_address'));
$result = $installer->getConnection()->fetchAll($stmt);

$table = $installer->getTable('customer/eav_attribute');
foreach ($result as $row) {
    $_visible = true;
    $_visibleOnFront = false;
    $_inputFilter = '';
    $_linesToDivideMultiline = 0;
    $_minLength = 0;
    $_maxLength = 0;
    if (in_array($row['attribute_code'], $visibleAttributes)) {
        $_visible = false;
    }
    $attributes = array(
        'attribute_id'              => $row['attribute_id'],
        'is_visible'                => $_visible,
        'is_visible_on_front'       => $_visibleOnFront,
        'input_filter'              => $_inputFilter,
        'lines_to_divide_multiline' => $_linesToDivideMultiline,
        'min_text_length'           => $_minLength,
        'max_text_length'           => $_maxLength
    );
    $installer->getConnection()->insert($table, $attributes);
}

$installer->endSetup();
