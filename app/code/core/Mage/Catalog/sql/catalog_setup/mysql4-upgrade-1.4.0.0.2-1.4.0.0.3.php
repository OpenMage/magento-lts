<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$attributes = [
    $installer->getAttributeId('catalog_product', 'cost'),
];

$sql    = $installer->getConnection()->quoteInto("SELECT * FROM `{$installer->getTable('catalog/eav_attribute')}` WHERE attribute_id IN (?)", $attributes);
$data   = $installer->getConnection()->fetchAll($sql);

foreach ($data as $row) {
    $row['apply_to'] = array_flip(explode(',', $row['apply_to']));
    unset($row['apply_to']['configurable']);
    $row['apply_to'] = implode(',', array_flip($row['apply_to']));

    $installer->run("UPDATE `{$installer->getTable('catalog/eav_attribute')}`
                SET `apply_to` = '{$row['apply_to']}'
                WHERE `attribute_id` = {$row['attribute_id']}");
}

$installer->endSetup();
