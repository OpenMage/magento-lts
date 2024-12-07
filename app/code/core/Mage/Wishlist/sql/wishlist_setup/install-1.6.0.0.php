<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'wishlist/wishlist'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('wishlist/wishlist'))
    ->addColumn('wishlist_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Wishlist ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer ID')
    ->addColumn('shared', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sharing flag (0 or 1)')
    ->addColumn('sharing_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
    ], 'Sharing encrypted code')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Last updated date')
    ->addIndex($installer->getIdxName('wishlist/wishlist', 'shared'), 'shared')
    ->addIndex(
        $installer->getIdxName('wishlist/wishlist', 'customer_id', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        'customer_id',
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName('wishlist/wishlist', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Wishlist main Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'wishlist/item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('wishlist/item'))
    ->addColumn('wishlist_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Wishlist item ID')
    ->addColumn('wishlist_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Wishlist ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => true,
    ], 'Store ID')
    ->addColumn('added_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Add date and time')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Short description of wish list item')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
    ], 'Qty')
    ->addIndex($installer->getIdxName('wishlist/item', 'wishlist_id'), 'wishlist_id')
    ->addForeignKey(
        $installer->getFkName('wishlist/item', 'wishlist_id', 'wishlist/wishlist', 'wishlist_id'),
        'wishlist_id',
        $installer->getTable('wishlist/wishlist'),
        'wishlist_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addIndex($installer->getIdxName('wishlist/item', 'product_id'), 'product_id')
    ->addForeignKey(
        $installer->getFkName('wishlist/item', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addIndex($installer->getIdxName('wishlist/item', 'store_id'), 'store_id')
    ->addForeignKey(
        $installer->getFkName('wishlist/item', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Wishlist items');
$installer->getConnection()->createTable($table);

/**
 * Create table 'wishlist/item_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('wishlist/item_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Id')
    ->addColumn('wishlist_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Wishlist Item Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Product Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Code')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => true,
    ], 'Value')
    ->addForeignKey(
        $installer->getFkName('wishlist/item_option', 'wishlist_item_id', 'wishlist/item', 'wishlist_item_id'),
        'wishlist_item_id',
        $installer->getTable('wishlist/item'),
        'wishlist_item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Wishlist Item Option Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
