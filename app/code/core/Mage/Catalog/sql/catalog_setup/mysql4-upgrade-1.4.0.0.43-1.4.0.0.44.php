<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;

$connection = $installer->getConnection();
$table      = $installer->getTable('catalog/category_product_indexer_idx');
$connection->addKey($table, 'IDX_PRODUCT_CATEGORY_STORE', ['product_id', 'category_id', 'store_id']);
