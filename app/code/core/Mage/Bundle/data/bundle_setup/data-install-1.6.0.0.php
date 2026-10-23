<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
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
