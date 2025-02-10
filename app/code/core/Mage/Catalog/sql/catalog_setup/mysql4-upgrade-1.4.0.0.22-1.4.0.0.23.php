<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey(
    $installer->getTable('catalog/product_link'),
    'IDX_UNIQUE',
    ['link_type_id', 'product_id', 'linked_product_id'],
    'unique',
);

$installer->endSetup();
