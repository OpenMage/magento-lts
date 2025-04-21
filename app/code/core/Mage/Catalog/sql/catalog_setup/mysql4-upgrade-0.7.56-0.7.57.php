<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$select = $installer->getConnection()->select()
    ->from($installer->getTable('catalog/category_product_index'), [
        'category_id' => 'category_id',
        'product_id'  => 'product_id',
        'is_parent'   => 'is_parent',
        'store_id'    => 'store_id',
        'rows_count'  => 'COUNT(*)'])
    ->group(['category_id' , 'product_id' , 'is_parent' , 'store_id'])
    ->having('rows_count > 1');
$query = $installer->getConnection()->query($select);

while ($row = $query->fetch()) {
    $sql = 'DELETE FROM `' . $installer->getTable('catalog/category_product_index') . '`'
        . ' WHERE category_id=? AND product_id=? AND is_parent=? AND store_id=?'
        . ' LIMIT ' . ($row['rows_count'] - 1);
    $installer->getConnection()->query($sql, [
        $row['category_id'],
        $row['product_id'],
        $row['is_parent'],
        $row['store_id'],
    ]);
}

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/category_product_index'),
    'UNQ_CATEGORY_PRODUCT',
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/category_product_index'),
    'UNQ_CATEGORY_PRODUCT',
    ['category_id', 'product_id', 'is_parent', 'store_id'],
    'unique',
);

$installer->endSetup();
