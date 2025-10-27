<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;

foreach (['news_from_date', 'custom_design_from'] as $attributeCode) {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCode,
        'backend_model',
        'catalog/product_attribute_backend_startdate',
    );
}
