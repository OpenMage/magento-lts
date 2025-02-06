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
