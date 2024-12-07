<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    unset($row['apply_to']['bundle']);
    $row['apply_to'] = implode(',', array_flip($row['apply_to']));

    $installer->run("UPDATE `{$installer->getTable('catalog/eav_attribute')}`
                SET `apply_to` = '{$row['apply_to']}'
                WHERE `attribute_id` = {$row['attribute_id']}");
}

$installer->endSetup();
