<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

$installer->updateAttribute(
    'catalog_product',
    'custom_layout_update',
    'is_global',
    Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
);
