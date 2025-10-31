<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'customer/entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Set Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Website Id')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Email')
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Group Id')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Active')
    ->addIndex(
        $installer->getIdxName('customer/entity', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer/entity', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer/entity', ['email', 'website_id']),
        ['email', 'website_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer/entity', ['website_id']),
        ['website_id'],
    )
    ->addForeignKey(
        $installer->getFkName('customer/entity', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer/entity', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Entity');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer/address_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/address_entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Set Id')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Increment Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => true,
    ], 'Parent Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => false,
    ], 'Updated At')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Active')
    ->addIndex(
        $installer->getIdxName('customer/address_entity', ['parent_id']),
        ['parent_id'],
    )
    ->addForeignKey(
        $installer->getFkName('customer/address_entity', 'parent_id', 'customer/entity', 'entity_id'),
        'parent_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Address Entity');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_address_entity_datetime'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_address_entity_datetime'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
        'nullable'  => false,
        'default' => $installer->getConnection()->getSuggestedZeroDate(),
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_address_entity_datetime',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_datetime', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_datetime', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_datetime', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_datetime', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_datetime', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_datetime', 'entity_id', 'customer/address_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/address_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'customer_address_entity_datetime',
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id',
        ),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Address Entity Datetime');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_address_entity_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_address_entity_decimal'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_address_entity_decimal',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_decimal', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_decimal', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_decimal', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_decimal', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_decimal', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_decimal', 'entity_id', 'customer/address_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/address_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_decimal', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Address Entity Decimal');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_address_entity_int'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_address_entity_int'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_address_entity_int',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_int', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_int', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_int', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_int', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_int', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_int', 'entity_id', 'customer/address_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/address_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_int', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Address Entity Int');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_address_entity_text'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_address_entity_text'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => false,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_address_entity_text',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_text', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_text', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_text', ['entity_id']),
        ['entity_id'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_text', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_text', 'entity_id', 'customer/address_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/address_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_text', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Address Entity Text');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_address_entity_varchar'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_address_entity_varchar'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_address_entity_varchar',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_varchar', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_varchar', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_varchar', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_address_entity_varchar', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_varchar', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_varchar', 'entity_id', 'customer/address_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/address_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_address_entity_varchar', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Address Entity Varchar');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_datetime'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_entity_datetime'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
        'nullable'  => false,
        'default' => $installer->getConnection()->getSuggestedZeroDate(),
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_entity_datetime',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_datetime', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_datetime', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_datetime', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_datetime', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_datetime', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_datetime', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_datetime', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Entity Datetime');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_entity_decimal'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_entity_decimal',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_decimal', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_decimal', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_decimal', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_decimal', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_decimal', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_decimal', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_decimal', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Entity Decimal');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_int'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_entity_int'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_entity_int',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_int', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_int', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_int', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_int', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_int', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_int', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_int', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Entity Int');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_text'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_entity_text'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => false,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_entity_text',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_text', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_text', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_text', ['entity_id']),
        ['entity_id'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_text', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_text', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_text', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Entity Text');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_varchar'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer_entity_varchar'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value')
    ->addIndex(
        $installer->getIdxName(
            'customer_entity_varchar',
            ['entity_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_varchar', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_varchar', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_varchar', ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('customer_entity_varchar', ['entity_id', 'attribute_id', 'value']),
        ['entity_id', 'attribute_id', 'value'],
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_varchar', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_varchar', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer_entity_varchar', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Entity Varchar');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_group'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/customer_group'))
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Customer Group Id')
    ->addColumn('customer_group_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
    ], 'Customer Group Code')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Tax Class Id')
    ->setComment('Customer Group');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer/eav_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Id')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Is Visible')
    ->addColumn('input_filter', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Input Filter')
    ->addColumn('multiline_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Multiline Count')
    ->addColumn('validate_rules', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Validate Rules')
    ->addColumn('is_system', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is System')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addColumn('data_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Data Model')
    ->addForeignKey(
        $installer->getFkName('customer/eav_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Eav Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer/form_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/form_attribute'))
    ->addColumn('form_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => false,
        'primary'   => true,
    ], 'Form Code')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Id')
    ->addIndex(
        $installer->getIdxName('customer/form_attribute', ['attribute_id']),
        ['attribute_id'],
    )
    ->addForeignKey(
        $installer->getFkName('customer/form_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Form Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer/eav_attribute_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/eav_attribute_website'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Website Id')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Visible')
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Is Required')
    ->addColumn('default_value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Default Value')
    ->addColumn('multiline_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Multiline Count')
    ->addIndex(
        $installer->getIdxName('customer/eav_attribute_website', ['website_id']),
        ['website_id'],
    )
    ->addForeignKey(
        $installer->getFkName('customer/eav_attribute_website', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('customer/eav_attribute_website', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Customer Eav Attribute Website');
$installer->getConnection()->createTable($table);

$installer->endSetup();

// insert default customer groups
$installer->getConnection()->insertForce($installer->getTable('customer/customer_group'), [
    'customer_group_id'     => 0,
    'customer_group_code'   => 'NOT LOGGED IN',
    'tax_class_id'          => 3,
]);
$installer->getConnection()->insertForce($installer->getTable('customer/customer_group'), [
    'customer_group_id'     => 1,
    'customer_group_code'   => 'General',
    'tax_class_id'          => 3,
]);
$installer->getConnection()->insertForce($installer->getTable('customer/customer_group'), [
    'customer_group_id'     => 2,
    'customer_group_code'   => 'Wholesale',
    'tax_class_id'          => 3,
]);
$installer->getConnection()->insertForce($installer->getTable('customer/customer_group'), [
    'customer_group_id'     => 3,
    'customer_group_code'   => 'Retailer',
    'tax_class_id'          => 3,
]);

$installer->installEntities();

$installer->installCustomerForms();
