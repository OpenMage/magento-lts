<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
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
$visibleAttributes = ['store_id', 'default_billing', 'default_shipping', 'confirmation'];
$stmt = $installer->getConnection()->select()
    ->from($installer->getTable('eav/attribute'), ['attribute_id', 'attribute_code'])
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

    $attributes = [
        'attribute_id'              => $row['attribute_id'],
        'is_visible'                => $_visible,
        'is_visible_on_front'       => $_visibleOnFront,
        'input_filter'              => $_inputFilter,
        'lines_to_divide_multiline' => $_linesToDivideMultiline,
        'min_text_length'           => $_minLength,
        'max_text_length'           => $_maxLength,
    ];
    $installer->getConnection()->insert($table, $attributes);
}

$installer->endSetup();
