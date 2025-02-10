<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

$productEntityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($productEntityTypeId, 'sku', 'backend_model', 'catalog/product_attribute_backend_sku');
