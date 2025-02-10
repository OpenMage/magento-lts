<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($entityTypeId, 'custom_layout_update', 'backend_model', 'catalog/attribute_backend_customlayoutupdate');

$entityTypeId = $installer->getEntityTypeId('catalog_category');
$installer->updateAttribute($entityTypeId, 'custom_layout_update', 'backend_model', 'catalog/attribute_backend_customlayoutupdate');
