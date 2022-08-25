<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */

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
        $row['store_id']
    ]);
}

$installer->getConnection()->dropKey(
    $installer->getTable('catalog/category_product_index'),
    'UNQ_CATEGORY_PRODUCT'
);
$installer->getConnection()->addKey(
    $installer->getTable('catalog/category_product_index'),
    'UNQ_CATEGORY_PRODUCT',
    ['category_id', 'product_id', 'is_parent', 'store_id'],
    'unique'
);

$installer->endSetup();
