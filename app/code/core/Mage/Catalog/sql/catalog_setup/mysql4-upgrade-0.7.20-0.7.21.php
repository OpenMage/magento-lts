<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$attributes = [
    $installer->getAttributeId('catalog_product', 'price'),
    $installer->getAttributeId('catalog_product', 'special_price'),
    $installer->getAttributeId('catalog_product', 'special_from_date'),
    $installer->getAttributeId('catalog_product', 'special_to_date'),
    $installer->getAttributeId('catalog_product', 'cost'),
    $installer->getAttributeId('catalog_product', 'tier_price'),
];

$sql    = $installer->getConnection()->quoteInto("SELECT * FROM `{$installer->getTable('eav_attribute')}` WHERE attribute_id IN (?)", $attributes);
$data   = $installer->getConnection()->fetchAll($sql);

foreach ($data as $row) {
    $row['apply_to'] = array_flip(explode(',', $row['apply_to']));
    unset($row['apply_to']['grouped']);
    $row['apply_to'] = implode(',', array_flip($row['apply_to']));

    $installer->run("UPDATE `{$installer->getTable('eav_attribute')}`
                SET `apply_to` = '{$row['apply_to']}'
                WHERE `attribute_id` = {$row['attribute_id']}");
}
