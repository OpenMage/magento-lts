<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'tag/tag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tag/tag'))
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Tag Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Name')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Status')
    ->addColumn('first_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'First Customer Id')
    ->addColumn('first_store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'First Store Id')
    ->addForeignKey(
        $installer->getFkName('tag/tag', 'first_customer_id', 'customer/entity', 'entity_id'),
        'first_customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->addForeignKey(
        $installer->getFkName('tag/tag', 'first_store_id', 'core/store', 'store_id'),
        'first_store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Tag');
$installer->getConnection()->createTable($table);

/**
 * Create table 'tag/relation'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tag/relation'))
    ->addColumn('tag_relation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Tag Relation Id')
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Tag Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Customer Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Store Id')
    ->addColumn('active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Active')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName('tag/relation', ['tag_id', 'customer_id', 'product_id', 'store_id'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['tag_id', 'customer_id', 'product_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('tag/relation', ['product_id']),
        ['product_id']
    )
    ->addIndex(
        $installer->getIdxName('tag/relation', ['tag_id']),
        ['tag_id']
    )
    ->addIndex(
        $installer->getIdxName('tag/relation', ['customer_id']),
        ['customer_id']
    )
    ->addIndex(
        $installer->getIdxName('tag/relation', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('tag/relation', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('tag/relation', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('tag/relation', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('tag/relation', 'tag_id', 'tag/tag', 'tag_id'),
        'tag_id',
        $installer->getTable('tag/tag'),
        'tag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Tag Relation');
$installer->getConnection()->createTable($table);

/**
 * Create table 'tag/summary'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tag/summary'))
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Tag Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('customers', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customers')
    ->addColumn('products', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Products')
    ->addColumn('uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Uses')
    ->addColumn('historical_uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Historical Uses')
    ->addColumn('popularity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Popularity')
    ->addColumn('base_popularity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Base Popularity')
    ->addIndex(
        $installer->getIdxName('tag/summary', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('tag/summary', ['tag_id']),
        ['tag_id']
    )
    ->addForeignKey(
        $installer->getFkName('tag/summary', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('tag/summary', 'tag_id', 'tag/tag', 'tag_id'),
        'tag_id',
        $installer->getTable('tag/tag'),
        'tag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Tag Summary');
$installer->getConnection()->createTable($table);

/**
 * Create table 'tag/properties'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tag/properties'))
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Tag Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('base_popularity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Base Popularity')
    ->addIndex(
        $installer->getIdxName('tag/properties', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('tag/properties', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('tag/properties', 'tag_id', 'tag/tag', 'tag_id'),
        'tag_id',
        $installer->getTable('tag/tag'),
        'tag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Tag Properties');
$installer->getConnection()->createTable($table);

$installer->endSetup();
