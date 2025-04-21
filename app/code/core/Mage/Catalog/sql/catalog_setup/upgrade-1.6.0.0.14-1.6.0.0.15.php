<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
