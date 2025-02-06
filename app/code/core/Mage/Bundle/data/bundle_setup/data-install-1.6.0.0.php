<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$fieldList = [
    'price','special_price','special_from_date','special_to_date',
    'minimal_price','cost','tier_price','weight',
];
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, $field, 'apply_to'));
    if (!in_array('bundle', $applyTo)) {
        $applyTo[] = 'bundle';
        $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $field, 'apply_to', implode(',', $applyTo));
    }
}

$applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'cost', 'apply_to'));
unset($applyTo[array_search('bundle', $applyTo)]);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'cost', 'apply_to', implode(',', $applyTo));
