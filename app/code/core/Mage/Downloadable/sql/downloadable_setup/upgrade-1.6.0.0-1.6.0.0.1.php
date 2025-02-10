<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @var Mage_Catalog_Model_Resource_Setup $installer
 */
$installer = $this;

$msrpEnabled = $installer->getAttribute('catalog_product', 'msrp_enabled', 'apply_to');
if ($msrpEnabled && strstr($msrpEnabled, Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute('catalog_product', 'msrp_enabled', [
        'apply_to'      => $msrpEnabled . ',' . Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
    ]);
}

$msrpDisplay = $installer->getAttribute('catalog_product', 'msrp_display_actual_price_type', 'apply_to');
if ($msrpDisplay && strstr($msrpEnabled, Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute('catalog_product', 'msrp_display_actual_price_type', [
        'apply_to'      => $msrpDisplay . ',' . Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
    ]);
}

$msrp = $installer->getAttribute('catalog_product', 'msrp', 'apply_to');
if ($msrp && strstr($msrpEnabled, Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute('catalog_product', 'msrp', [
        'apply_to'      => $msrp . ',' . Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
    ]);
}
