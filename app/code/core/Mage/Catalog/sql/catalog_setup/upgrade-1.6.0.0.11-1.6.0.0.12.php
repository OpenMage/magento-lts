<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup $installer
 */
$installer  = $this;

$indexFields = ['website_id', 'customer_group_id', 'min_price'];
$installer->getConnection()->addIndex(
    $installer->getTable('catalog/product_index_price'),
    $installer->getIdxName('catalog/product_index_price', $indexFields),
    $indexFields,
);
