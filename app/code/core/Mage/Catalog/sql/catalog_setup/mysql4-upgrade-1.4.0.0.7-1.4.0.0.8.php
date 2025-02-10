<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

$installer->updateAttribute('catalog_category', 'url_key', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_category', 'url_path', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_product', 'url_key', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_product', 'url_path', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
