<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

foreach (['news_from_date', 'custom_design_from'] as $attributeCode) {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attributeCode,
        'backend_model',
        'catalog/product_attribute_backend_startdate',
    );
}
