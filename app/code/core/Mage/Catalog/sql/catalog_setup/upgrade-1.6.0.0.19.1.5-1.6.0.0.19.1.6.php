<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$attributeId = 'custom_layout_update';
$entitiesToUpgrade = [
    $installer->getEntityTypeId('catalog_product'),
    $installer->getEntityTypeId('catalog_category'),
];
foreach ($entitiesToUpgrade as $entityTypeId) {
    if ($this->getAttributeId($entityTypeId, $attributeId)) {
        $installer->updateAttribute(
            $entityTypeId,
            $attributeId,
            'backend_model',
            'catalog/attribute_backend_customlayoutupdate',
        );
    }
}
