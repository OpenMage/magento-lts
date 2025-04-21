<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $this */
$installer = $this;
$installer->startSetup();

// make attribute 'weight' not applicable to downloadable products
$applyTo = explode(',', $installer->getAttribute('catalog_product', 'weight', 'apply_to'));
if (in_array('downloadable', $applyTo)) {
    $newApplyTo = [];
    foreach ($applyTo as $key => $value) {
        if ($value != 'downloadable') {
            $newApplyTo[] = $value;
        }
    }
    $installer->updateAttribute('catalog_product', 'weight', 'apply_to', implode(',', $newApplyTo));
} else {
    $installer->updateAttribute('catalog_product', 'weight', 'apply_to', implode(',', $applyTo));
}

// remove 'weight' values for downloadable products if there were any created
$attributeId = $installer->getAttributeId('catalog_product', 'weight');
$installer->run("
    DELETE FROM {$installer->getTable('catalog_product_entity_decimal')}
    WHERE (entity_id in (
        SELECT entity_id FROM {$installer->getTable('catalog/product')} WHERE type_id = 'downloadable'
    )) and attribute_id = {$attributeId}
");

$installer->endSetup();
