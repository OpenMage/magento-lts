<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($entityTypeId, 'meta_description', 'frontend_class', 'validate-length maximum-length-255');
