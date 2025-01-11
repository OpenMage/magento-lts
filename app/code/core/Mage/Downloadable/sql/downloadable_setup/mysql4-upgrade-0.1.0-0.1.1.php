<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
