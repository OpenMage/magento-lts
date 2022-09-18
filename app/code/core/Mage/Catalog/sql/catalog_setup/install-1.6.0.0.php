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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'catalog/product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Set ID')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'default'   => Mage_Catalog_Model_Product_Type::DEFAULT_TYPE,
    ], 'Type ID')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'SKU')
    ->addColumn('has_options', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Has Options')
    ->addColumn('required_options', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Required Options')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Update Time')
    ->addIndex(
        $installer->getIdxName('catalog/product', ['entity_type_id']),
        ['entity_type_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product', ['attribute_set_id']),
        ['attribute_set_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product', ['sku']),
        ['sku']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id',
        $installer->getTable('eav/attribute_set'),
        'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/product', 'datetime')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/product', 'datetime']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/product', 'datetime'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'datetime'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'datetime'], ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'datetime'], ['entity_id']),
        ['entity_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'datetime'],
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'datetime'],
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'datetime'],
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Datetime Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/product', 'decimal')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/product', 'decimal']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/product', 'decimal'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'decimal'], ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'decimal'], ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'decimal'], ['attribute_id']),
        ['attribute_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'decimal'],
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'decimal'],
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'decimal'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Decimal Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/product', 'int')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/product', 'int']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/product', 'int'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'int'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'int'], ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'int'], ['entity_id']),
        ['entity_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'int'],
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'int'],
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            ['catalog/product', 'int'],
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Integer Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/product', 'text')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/product', 'text']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/product', 'text'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'text'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'text'], ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'text'], ['entity_id']),
        ['entity_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'text'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'text'], 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'text'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Text Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/product', 'varchar')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/product', 'varchar']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/product', 'varchar'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'varchar'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'varchar'], ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'varchar'], ['entity_id']),
        ['entity_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'varchar'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'varchar'], 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'varchar'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Varchar Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/product', 'gallery')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/product', 'gallery']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/product', 'gallery'],
            ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'gallery'], ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'gallery'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/product', 'gallery'], ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'gallery'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'gallery'], 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/product', 'gallery'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Gallery Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attriute Set ID')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Parent Category ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
    ], 'Update Time')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Tree Path')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
    ], 'Position')
    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Tree Level')
    ->addColumn('children_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
    ], 'Child Count')
    ->addIndex(
        $installer->getIdxName('catalog/category', ['level']),
        ['level']
    )
    ->setComment('Catalog Category Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/category', 'datetime')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/category', 'datetime']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/category', 'datetime'],
            ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'datetime'], ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'datetime'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'datetime'], ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'datetime'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'datetime'], 'entity_id', 'catalog/category', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'datetime'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Category Datetime Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/category', 'decimal')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/category', 'decimal']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/category', 'decimal'],
            ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'decimal'], ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'decimal'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'decimal'], ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'decimal'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'decimal'], 'entity_id', 'catalog/category', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'decimal'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Category Decimal Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/category', 'int')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/category', 'int']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/category', 'int'],
            ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'int'], ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'int'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'int'], ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'int'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'int'], 'entity_id', 'catalog/category', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'int'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Category Integer Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/category', 'text')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/category', 'text']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/category', 'text'],
            ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'text'], ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'text'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'text'], ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'text'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'text'], 'entity_id', 'catalog/category', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'text'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Category Text Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('catalog/category', 'varchar')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['catalog/category', 'varchar']))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            ['catalog/category', 'varchar'],
            ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_type_id', 'entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'varchar'], ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'varchar'], ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName(['catalog/category', 'varchar'], ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'varchar'], 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'varchar'], 'entity_id', 'catalog/category', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(['catalog/category', 'varchar'], 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Category Varchar Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_product'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
/*    ->addIndex($installer->getIdxName('catalog/category_product', array('category_id')),
        array('category_id'))*/
    ->addIndex(
        $installer->getIdxName('catalog/category_product', ['product_id']),
        ['product_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/category_product', 'category_id', 'catalog/category', 'entity_id'),
        'category_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/category_product', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product To Category Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_product_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_product_index'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Position')
    ->addColumn('is_parent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Parent')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Visibility')
    ->addIndex(
        $installer->getIdxName(
            'catalog/category_product_index',
            ['product_id', 'store_id', 'category_id', 'visibility']
        ),
        ['product_id', 'store_id', 'category_id', 'visibility']
    )
    ->addIndex(
        $installer->getIdxName(
            'catalog/category_product_index',
            ['store_id', 'category_id', 'visibility', 'is_parent', 'position']
        ),
        ['store_id', 'category_id', 'visibility', 'is_parent', 'position']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/category_product_index', 'category_id', 'catalog/category', 'entity_id'),
        'category_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/category_product_index', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/category_product_index', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Category Product Index');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/compare_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/compare_item'))
    ->addColumn('catalog_compare_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Compare Item ID')
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Visitor ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Customer ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Store ID')
    ->addIndex(
        $installer->getIdxName('catalog/compare_item', ['customer_id']),
        ['customer_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/compare_item', ['product_id']),
        ['product_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/compare_item', ['visitor_id', 'product_id']),
        ['visitor_id', 'product_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/compare_item', ['customer_id', 'product_id']),
        ['customer_id', 'product_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/compare_item', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/compare_item', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/compare_item', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/compare_item', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Compare Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_website'))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Product ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addIndex(
        $installer->getIdxName('catalog/product_website', ['website_id']),
        ['website_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_website', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_website', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product To Website Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_enabled_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_enabled_index'))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Visibility')
    ->addIndex(
        $installer->getIdxName('catalog/product_enabled_index', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_enabled_index', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_enabled_index', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Visibility Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_link_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_link_type'))
    ->addColumn('link_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Link Type ID')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
        'default'   => null,
    ], 'Code')
    ->setComment('Catalog Product Link Type Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_link'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Link ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('linked_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Linked Product ID')
    ->addColumn('link_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Link Type ID')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_link',
            ['link_type_id', 'product_id', 'linked_product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['link_type_id', 'product_id', 'linked_product_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_link', ['product_id']),
        ['product_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_link', ['linked_product_id']),
        ['linked_product_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_link', ['link_type_id']),
        ['link_type_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_link', 'linked_product_id', 'catalog/product', 'entity_id'),
        'linked_product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_link', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_link', 'link_type_id', 'catalog/product_link_type', 'link_type_id'),
        'link_type_id',
        $installer->getTable('catalog/product_link_type'),
        'link_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product To Product Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_link_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_link_attribute'))
    ->addColumn('product_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Product Link Attribute ID')
    ->addColumn('link_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Link Type ID')
    ->addColumn('product_link_attribute_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
        'default'   => null,
    ], 'Product Link Attribute Code')
    ->addColumn('data_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
        'default'   => null,
    ], 'Data Type')
    ->addIndex(
        $installer->getIdxName('catalog/product_link_attribute', ['link_type_id']),
        ['link_type_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_link_attribute',
            'link_type_id',
            'catalog/product_link_type',
            'link_type_id'
        ),
        'link_type_id',
        $installer->getTable('catalog/product_link_type'),
        'link_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Link Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_link_attribute_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_link_attribute_decimal'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('product_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Product Link Attribute ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'unsigned'  => true,
    ], 'Link ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_link_attribute_decimal', ['product_link_attribute_id']),
        ['product_link_attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_link_attribute_decimal', ['link_id']),
        ['link_id']
    )
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_link_attribute_decimal',
            ['product_link_attribute_id', 'link_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['product_link_attribute_id', 'link_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_link_attribute_decimal',
            'link_id',
            'catalog/product_link',
            'link_id'
        ),
        'link_id',
        $installer->getTable('catalog/product_link'),
        'link_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_link_attribute_decimal',
            'product_link_attribute_id',
            'catalog/product_link_attribute',
            'product_link_attribute_id'
        ),
        'product_link_attribute_id',
        $installer->getTable('catalog/product_link_attribute'),
        'product_link_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Link Decimal Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_link_attribute_int'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_link_attribute_int'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('product_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Product Link Attribute ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'unsigned'  => true,
    ], 'Link ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_link_attribute_int', ['product_link_attribute_id']),
        ['product_link_attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_link_attribute_int', ['link_id']),
        ['link_id']
    )
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_link_attribute_int',
            ['product_link_attribute_id', 'link_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['product_link_attribute_id', 'link_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_link_attribute_int',
            'link_id',
            'catalog/product_link',
            'link_id'
        ),
        'link_id',
        $installer->getTable('catalog/product_link'),
        'link_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_link_attribute_int',
            'product_link_attribute_id',
            'catalog/product_link_attribute',
            'product_link_attribute_id'
        ),
        'product_link_attribute_id',
        $installer->getTable('catalog/product_link_attribute'),
        'product_link_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Link Integer Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_link_attribute_varchar'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_link_attribute_varchar'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('product_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product Link Attribute ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'unsigned'  => true,
    ], 'Link ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_link_attribute_varchar', ['product_link_attribute_id']),
        ['product_link_attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_link_attribute_varchar', ['link_id']),
        ['link_id']
    )
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_link_attribute_varchar',
            ['product_link_attribute_id', 'link_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['product_link_attribute_id', 'link_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_link_attribute_varchar',
            'link_id',
            'catalog/product_link',
            'link_id'
        ),
        'link_id',
        $installer->getTable('catalog/product_link'),
        'link_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_link_attribute_varchar',
            'product_link_attribute_id',
            'catalog/product_link_attribute',
            'product_link_attribute_id'
        ),
        'product_link_attribute_id',
        $installer->getTable('catalog/product_link_attribute'),
        'product_link_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Link Varchar Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_super_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_super_attribute'))
    ->addColumn('product_super_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Product Super Attribute ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
    ->addIndex(
        $installer->getIdxName('catalog/product_super_attribute', ['product_id']),
        ['product_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_super_attribute', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Catalog Product Super Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_super_attribute_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_super_attribute_label'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('product_super_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product Super Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('use_default', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Use Default Value')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_super_attribute_label',
            ['product_super_attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['product_super_attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_super_attribute_label', ['product_super_attribute_id']),
        ['product_super_attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_super_attribute_label', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_super_attribute_label',
            'product_super_attribute_id',
            'catalog/product_super_attribute',
            'product_super_attribute_id'
        ),
        'product_super_attribute_id',
        $installer->getTable('catalog/product_super_attribute'),
        'product_super_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_super_attribute_label', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Super Attribute Label Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_super_attribute_pricing'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_super_attribute_pricing'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('product_super_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product Super Attribute ID')
    ->addColumn('value_index', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Value Index')
    ->addColumn('is_percent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Is Percent')
    ->addColumn('pricing_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Pricing Value')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Website ID')
    ->addIndex(
        $installer->getIdxName('catalog/product_super_attribute_pricing', ['product_super_attribute_id']),
        ['product_super_attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_super_attribute_pricing', ['website_id']),
        ['website_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_super_attribute_pricing',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_super_attribute_pricing',
            'product_super_attribute_id',
            'catalog/product_super_attribute',
            'product_super_attribute_id'
        ),
        'product_super_attribute_id',
        $installer->getTable('catalog/product_super_attribute'),
        'product_super_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Super Attribute Pricing Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_super_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_super_link'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Link ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Parent ID')
    ->addIndex(
        $installer->getIdxName('catalog/product_super_link', ['parent_id']),
        ['parent_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_super_link', ['product_id']),
        ['product_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_super_link', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_super_link', 'parent_id', 'catalog/product', 'entity_id'),
        'parent_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Super Link Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_attribute_tier_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_attribute_tier_price'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('all_groups', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Applicable To All Customer Groups')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer Group ID')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '1.0000',
    ], 'QTY')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Value')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Website ID')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_attribute_tier_price',
            ['entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_tier_price', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_tier_price', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_tier_price', ['website_id']),
        ['website_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_tier_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id'
        ),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_tier_price',
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_tier_price',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Tier Price Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_attribute_media_gallery'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_attribute_media_gallery'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_media_gallery', ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_media_gallery', ['entity_id']),
        ['entity_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_media_gallery',
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_media_gallery',
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Media Gallery Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_attribute_media_gallery_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_attribute_media_gallery_value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Value ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Label')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Position')
    ->addColumn('disabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Disabled')
    ->addIndex(
        $installer->getIdxName('catalog/product_attribute_media_gallery_value', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_media_gallery_value',
            'value_id',
            'catalog/product_attribute_media_gallery',
            'value_id'
        ),
        'value_id',
        $installer->getTable('catalog/product_attribute_media_gallery'),
        'value_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_attribute_media_gallery_value',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Media Gallery Attribute Value Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => true,
        'default'   => null,
    ], 'Type')
    ->addColumn('is_require', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Required')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'SKU')
    ->addColumn('max_characters', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Max Characters')
    ->addColumn('file_extension', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'File Extension')
    ->addColumn('image_size_x', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned' => true,
    ], 'Image Size X')
    ->addColumn('image_size_y', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned' => true,
    ], 'Image Size Y')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName('catalog/product_option', ['product_id']),
        ['product_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_option', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Option Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_option_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_option_price'))
    ->addColumn('option_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Price ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Option ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Price')
    ->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_TEXT, 7, [
        'nullable'  => false,
        'default'   => 'fixed',
    ], 'Price Type')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_option_price',
            ['option_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['option_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_price', ['option_id']),
        ['option_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_price', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_price',
            'option_id',
            'catalog/product_option',
            'option_id'
        ),
        'option_id',
        $installer->getTable('catalog/product_option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_price',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Option Price Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_option_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_option_title'))
    ->addColumn('option_title_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Title ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Option ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Title')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_option_title',
            ['option_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['option_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_title', ['option_id']),
        ['option_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_title', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_title',
            'option_id',
            'catalog/product_option',
            'option_id'
        ),
        'option_id',
        $installer->getTable('catalog/product_option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_title',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Option Title Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_option_type_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_option_type_value'))
    ->addColumn('option_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Type ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Option ID')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
    ], 'SKU')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName('catalog/product_option_type_value', ['option_id']),
        ['option_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_type_value',
            'option_id',
            'catalog/product_option',
            'option_id'
        ),
        'option_id',
        $installer->getTable('catalog/product_option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Option Type Value Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_option_type_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_option_type_price'))
    ->addColumn('option_type_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Type Price ID')
    ->addColumn('option_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Option Type ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Price')
    ->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_TEXT, 7, [
        'nullable'  => false,
        'default'   => 'fixed',
    ], 'Price Type')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_option_type_price',
            ['option_type_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['option_type_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_type_price', ['option_type_id']),
        ['option_type_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_type_price', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_type_price',
            'option_type_id',
            'catalog/product_option_type_value',
            'option_type_id'
        ),
        'option_type_id',
        $installer->getTable('catalog/product_option_type_value'),
        'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_type_price',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Option Type Price Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_option_type_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_option_type_title'))
    ->addColumn('option_type_title_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Type Title ID')
    ->addColumn('option_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Option Type ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Title')
    ->addIndex(
        $installer->getIdxName(
            'catalog/product_option_type_title',
            ['option_type_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['option_type_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_type_title', ['option_type_id']),
        ['option_type_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_option_type_title', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_option_type_title',
            'option_type_id',
            'catalog/product_option_type_value',
            'option_type_id'
        ),
        'option_type_id',
        $installer->getTable('catalog/product_option_type_value'),
        'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_option_type_title', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Option Type Title Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/eav_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute ID')
    ->addColumn('frontend_input_renderer', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Frontend Input Renderer')
    ->addColumn('is_global', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Global')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Visible')
    ->addColumn('is_searchable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Searchable')
    ->addColumn('is_filterable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Filterable')
    ->addColumn('is_comparable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Comparable')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Visible On Front')
    ->addColumn('is_html_allowed_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is HTML Allowed On Front')
    ->addColumn('is_used_for_price_rules', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Used For Price Rules')
    ->addColumn('is_filterable_in_search', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Filterable In Search')
    ->addColumn('used_in_product_listing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Used In Product Listing')
    ->addColumn('used_for_sort_by', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Used For Sorting')
    ->addColumn('is_configurable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Configurable')
    ->addColumn('apply_to', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Apply To')
    ->addColumn('is_visible_in_advanced_search', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Visible In Advanced Search')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
    ->addColumn('is_wysiwyg_enabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is WYSIWYG Enabled')
    ->addColumn('is_used_for_promo_rules', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Used For Promo Rules')
    ->addIndex(
        $installer->getIdxName('catalog/eav_attribute', ['used_for_sort_by']),
        ['used_for_sort_by']
    )
    ->addIndex(
        $installer->getIdxName('catalog/eav_attribute', ['used_in_product_listing']),
        ['used_in_product_listing']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/eav_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog EAV Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_relation'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_relation'))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Parent ID')
    ->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Child ID')
    ->addIndex(
        $installer->getIdxName('catalog/product_relation', ['child_id']),
        ['child_id']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_relation', 'child_id', 'catalog/product', 'entity_id'),
        'child_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_relation', 'parent_id', 'catalog/product', 'entity_id'),
        'parent_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Relation Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_index_eav'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_index_eav'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav', ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav', ['value']),
        ['value']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_index_eav', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_index_eav', 'entity_id', 'catalog/product', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_index_eav', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product EAV Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_index_eav_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_index_eav_decimal'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'primary'   => true,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav_decimal', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav_decimal', ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav_decimal', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_eav_decimal', ['value']),
        ['value']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_eav_decimal',
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_eav_decimal',
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_eav_decimal',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product EAV Decimal Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_index_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_index_price'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Tax Class ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('final_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Final Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addIndex(
        $installer->getIdxName('catalog/product_index_price', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_price', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_price', ['min_price']),
        ['min_price']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id'
        ),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_price',
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_price',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Price Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_index_tier_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_index_tier_price'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addIndex(
        $installer->getIdxName('catalog/product_index_tier_price', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_index_tier_price', ['website_id']),
        ['website_id']
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_tier_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id'
        ),
        'customer_group_id',
        $installer->getTable('customer/customer_group'),
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_tier_price',
            'entity_id',
            'catalog/product',
            'entity_id'
        ),
        'entity_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName(
            'catalog/product_index_tier_price',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Tier Price Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_index_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_index_website'))
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('website_date', Varien_Db_Ddl_Table::TYPE_DATE, null, [
    ], 'Website Date')
    ->addColumn('rate', Varien_Db_Ddl_Table::TYPE_FLOAT, null, [
        'default'   => '1.0000',
    ], 'Rate')
    ->addIndex(
        $installer->getIdxName('catalog/product_index_website', ['website_date']),
        ['website_date']
    )
    ->addForeignKey(
        $installer->getFkName('catalog/product_index_website', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Catalog Product Website Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_cfg_option_aggregate_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_idx'))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Parent ID')
    ->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Child ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Config Option Aggregate Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_cfg_option_aggregate_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp'))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Parent ID')
    ->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Child ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Config Option Aggregate Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_cfg_option_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_cfg_option_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Config Option Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_cfg_option_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_cfg_option_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Config Option Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_final_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_final_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Tax Class ID')
    ->addColumn('orig_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Original Price')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addColumn('base_tier', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tier')
    ->setComment('Catalog Product Price Indexer Final Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_final_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_final_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Tax Class ID')
    ->addColumn('orig_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Original Price')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addColumn('base_tier', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Base Tier')
    ->setComment('Catalog Product Price Indexer Final Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_option_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_option_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Option Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_option_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_option_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Option Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_option_aggregate_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_option_aggregate_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Option ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Option Aggregate Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_option_aggregate_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_option_aggregate_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
    ], 'Option ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->setComment('Catalog Product Price Indexer Option Aggregate Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_eav_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_eav_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_idx', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_idx', ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_idx', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_idx', ['value']),
        ['value']
    )
    ->setComment('Catalog Product EAV Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_eav_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_eav_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_tmp', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_tmp', ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_tmp', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_indexer_tmp', ['value']),
        ['value']
    )
    ->setComment('Catalog Product EAV Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_eav_decimal_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_eav_decimal_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'primary'   => true,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_idx', ['value']),
        ['value']
    )
    ->setComment('Catalog Product EAV Decimal Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_eav_decimal_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_eav_decimal_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'primary'   => true,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['attribute_id']),
        ['attribute_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_eav_decimal_indexer_tmp', ['value']),
        ['value']
    )
    ->setComment('Catalog Product EAV Decimal Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Tax Class ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('final_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Final Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addIndex(
        $installer->getIdxName('catalog/product_price_indexer_idx', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_price_indexer_idx', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_price_indexer_idx', ['min_price']),
        ['min_price']
    )
    ->setComment('Catalog Product Price Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/product_price_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/product_price_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Tax Class ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Price')
    ->addColumn('final_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Final Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
    ], 'Tier Price')
    ->addIndex(
        $installer->getIdxName('catalog/product_price_indexer_tmp', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_price_indexer_tmp', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('catalog/product_price_indexer_tmp', ['min_price']),
        ['min_price']
    )
    ->setComment('Catalog Product Price Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_product_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_product_indexer_idx'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
    ->addColumn('is_parent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Parent')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Visibility')
    ->addIndex(
        $installer->getIdxName(
            'catalog/category_product_indexer_idx',
            ['product_id', 'category_id', 'store_id']
        ),
        ['product_id', 'category_id', 'store_id']
    )
    ->setComment('Catalog Category Product Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_product_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_product_indexer_tmp'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Position')
    ->addColumn('is_parent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is Parent')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Visibility')
    ->setComment('Catalog Category Product Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_product_enabled_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_product_enabled_indexer_idx'))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Visibility')
    ->addIndex(
        $installer->getIdxName('catalog/category_product_enabled_indexer_idx', ['product_id']),
        ['product_id']
    )
    ->setComment('Catalog Category Product Enabled Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_product_enabled_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_product_enabled_indexer_tmp'))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Visibility')
    ->addIndex(
        $installer->getIdxName('catalog/category_product_enabled_indexer_tmp', ['product_id']),
        ['product_id']
    )
    ->setComment('Catalog Category Product Enabled Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_anchor_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_anchor_indexer_idx'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Path')
    ->addIndex(
        $installer->getIdxName('catalog/category_anchor_indexer_idx', ['category_id']),
        ['category_id']
    )
    ->setComment('Catalog Category Anchor Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_anchor_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_anchor_indexer_tmp'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Path')
    ->addIndex(
        $installer->getIdxName('catalog/category_anchor_indexer_tmp', ['category_id']),
        ['category_id']
    )
    ->setComment('Catalog Category Anchor Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_anchor_products_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_anchor_products_indexer_idx'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
    ], 'Position')
    ->setComment('Catalog Category Anchor Product Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/category_anchor_products_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog/category_anchor_products_indexer_tmp'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Product ID')
    ->setComment('Catalog Category Anchor Product Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Modify core/url_rewrite table
 *
 */
$installer->getConnection()->addColumn($installer->getTable('core/url_rewrite'), 'category_id', [
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'comment'   => 'Category Id'
]);
$installer->getConnection()->addColumn($installer->getTable('core/url_rewrite'), 'product_id', [
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'comment'   => 'Product Id'
]);
$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'category_id', 'catalog/category', 'entity_id'),
    $installer->getTable('core/url_rewrite'),
    'category_id',
    $installer->getTable('catalog/category'),
    'entity_id'
);
$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'product_id', 'catalog/category', 'entity_id'),
    $installer->getTable('core/url_rewrite'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id'
);

$installer->endSetup();

$installer->installEntities();
