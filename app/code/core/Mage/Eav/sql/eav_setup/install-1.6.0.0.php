<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'eav/entity_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity_type'))
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Type Id')
    ->addColumn('entity_type_code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => false,
    ], 'Entity Type Code')
    ->addColumn('entity_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Entity Model')
    ->addColumn('attribute_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
    ], 'Attribute Model')
    ->addColumn('entity_table', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Entity Table')
    ->addColumn('value_table_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Value Table Prefix')
    ->addColumn('entity_id_field', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Entity Id Field')
    ->addColumn('is_data_sharing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ], 'Defines Is Data Sharing')
    ->addColumn('data_sharing_key', Varien_Db_Ddl_Table::TYPE_TEXT, 100, [
        'default'   => 'default',
    ], 'Data Sharing Key')
    ->addColumn('default_attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Default Attribute Set Id')
    ->addColumn('increment_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => '',
    ], 'Increment Model')
    ->addColumn('increment_per_store', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Increment Per Store')
    ->addColumn('increment_pad_length', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '8',
    ], 'Increment Pad Length')
    ->addColumn('increment_pad_char', Varien_Db_Ddl_Table::TYPE_TEXT, 1, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Increment Pad Char')
    ->addColumn('additional_attribute_table', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => '',
    ], 'Additional Attribute Table')
    ->addColumn('entity_attribute_collection', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Entity Attribute Collection')
    ->addIndex(
        $installer->getIdxName('eav/entity_type', ['entity_type_code']),
        ['entity_type_code'],
    )
    ->setComment('Eav Entity Type');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity'))
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
        'nullable'  => true,
        'default'   => null,
    ], 'Increment Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Parent Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
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
    ], 'Defines Is Entity Active')
    ->addIndex(
        $installer->getIdxName('eav/entity', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/entity', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/entity', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('eav/entity', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'datetime')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['eav/entity_value_prefix', 'datetime']))
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
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, [
        'nullable'  => false,
        'default' => $installer->getConnection()->getSuggestedZeroDate(),
    ], 'Attribute Value')
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['attribute_id', 'value']),
        ['attribute_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['entity_type_id', 'value']),
        ['entity_type_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(
            ['eav/entity_value_prefix', 'datetime'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'datetime'],
            'entity_id',
            'eav/entity',
            'entity_id',
        ),
        'entity_id',
        $installer->getTable('eav/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'datetime'],
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
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'datetime'],
            'store_id',
            'core/store',
            'store_id',
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'decimal')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['eav/entity_value_prefix', 'decimal']))
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
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Attribute Value')
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['attribute_id', 'value']),
        ['attribute_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['entity_type_id', 'value']),
        ['entity_type_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(
            ['eav/entity_value_prefix', 'decimal'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'decimal'],
            'entity_id',
            'eav/entity',
            'entity_id',
        ),
        'entity_id',
        $installer->getTable('eav/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'decimal'],
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
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'decimal'],
            'store_id',
            'core/store',
            'store_id',
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'int')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['eav/entity_value_prefix', 'int']))
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
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Value')
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['attribute_id', 'value']),
        ['attribute_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['entity_type_id', 'value']),
        ['entity_type_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(
            ['eav/entity_value_prefix', 'int'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'int'],
            'entity_id',
            'eav/entity',
            'entity_id',
        ),
        'entity_id',
        $installer->getTable('eav/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'int'],
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
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'int'],
            'store_id',
            'core/store',
            'store_id',
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'text')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['eav/entity_value_prefix', 'text']))
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
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
        'nullable'  => false,
    ], 'Attribute Value')
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName(
            ['eav/entity_value_prefix', 'text'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'text'],
            'entity_id',
            'eav/entity',
            'entity_id',
        ),
        'entity_id',
        $installer->getTable('eav/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'text'],
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
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'text'],
            'store_id',
            'core/store',
            'store_id',
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'varchar')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(['eav/entity_value_prefix', 'varchar']))
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
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Attribute Value')
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['entity_id']),
        ['entity_id'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['attribute_id', 'value']),
        ['attribute_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['entity_type_id', 'value']),
        ['entity_type_id', 'value'],
    )
    ->addIndex(
        $installer->getIdxName(
            ['eav/entity_value_prefix', 'varchar'],
            ['entity_id', 'attribute_id', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_id', 'attribute_id', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'varchar'],
            'entity_id',
            'eav/entity',
            'entity_id',
        ),
        'entity_id',
        $installer->getTable('eav/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'varchar'],
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
    ->addForeignKey(
        $installer->getFkName(
            ['eav/entity_value_prefix', 'varchar'],
            'store_id',
            'core/store',
            'store_id',
        ),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Attribute Code')
    ->addColumn('attribute_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Attribute Model')
    ->addColumn('backend_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Backend Model')
    ->addColumn('backend_type', Varien_Db_Ddl_Table::TYPE_TEXT, 8, [
        'nullable'  => false,
        'default'   => 'static',
    ], 'Backend Type')
    ->addColumn('backend_table', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Backend Table')
    ->addColumn('frontend_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Frontend Model')
    ->addColumn('frontend_input', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
    ], 'Frontend Input')
    ->addColumn('frontend_label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Frontend Label')
    ->addColumn('frontend_class', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Frontend Class')
    ->addColumn('source_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Source Model')
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Defines Is Required')
    ->addColumn('is_user_defined', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Defines Is User Defined')
    ->addColumn('default_value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', [
    ], 'Default Value')
    ->addColumn('is_unique', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Defines Is Unique')
    ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
    ], 'Note')
    ->addIndex(
        $installer->getIdxName(
            'eav/attribute',
            ['entity_type_id', 'attribute_code'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_type_id', 'attribute_code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('eav/attribute', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/attribute', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/entity_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity_store'))
    ->addColumn('entity_store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Store Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('increment_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => true,
    ], 'Increment Prefix')
    ->addColumn('increment_last_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [
        'nullable'  => true,
    ], 'Last Incremented Id')
    ->addIndex(
        $installer->getIdxName('eav/entity_store', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/entity_store', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/entity_store', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('eav/entity_store', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity Store');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_set'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_set'))
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Set Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Entity Type Id')
    ->addColumn('attribute_set_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Attribute Set Name')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/attribute_set',
            ['entity_type_id', 'attribute_set_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_type_id', 'attribute_set_name'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('eav/attribute_set', ['entity_type_id', 'sort_order']),
        ['entity_type_id', 'sort_order'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute_set', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Attribute Set');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_group'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_group'))
    ->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Group Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Set Id')
    ->addColumn('attribute_group_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Attribute Group Name')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addColumn('default_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'default'   => '0',
    ], 'Default Id')
    ->addIndex(
        $installer->getIdxName(
            'eav/attribute_group',
            ['attribute_set_id', 'attribute_group_name'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['attribute_set_id', 'attribute_group_name'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('eav/attribute_group', ['attribute_set_id', 'sort_order']),
        ['attribute_set_id', 'sort_order'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'eav/attribute_group',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id',
        ),
        'attribute_set_id',
        $installer->getTable('eav/attribute_set'),
        'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Attribute Group');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/entity_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity_attribute'))
    ->addColumn('entity_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Attribute Id')
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
    ->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Group Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/entity_attribute',
            ['attribute_set_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['attribute_set_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName(
            'eav/entity_attribute',
            ['attribute_group_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['attribute_group_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('eav/entity_attribute', ['attribute_set_id', 'sort_order']),
        ['attribute_set_id', 'sort_order'],
    )
    ->addIndex(
        $installer->getIdxName('eav/entity_attribute', ['attribute_id']),
        ['attribute_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/entity_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName(
            'eav/entity_attribute',
            'attribute_group_id',
            'eav/attribute_group',
            'attribute_group_id',
        ),
        'attribute_group_id',
        $installer->getTable('eav/attribute_group'),
        'attribute_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Entity Attributes');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Option Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName('eav/attribute_option', ['attribute_id']),
        ['attribute_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute_option', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Attribute Option');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_option_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_option_value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Value Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Option Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('eav/attribute_option_value', ['option_id']),
        ['option_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/attribute_option_value', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute_option_value', 'option_id', 'eav/attribute_option', 'option_id'),
        'option_id',
        $installer->getTable('eav/attribute_option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute_option_value', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Attribute Option Value');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_label'))
    ->addColumn('attribute_label_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Attribute Label Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Value')
    ->addIndex(
        $installer->getIdxName('eav/attribute_label', ['attribute_id']),
        ['attribute_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/attribute_label', ['store_id']),
        ['store_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/attribute_label', ['attribute_id', 'store_id']),
        ['attribute_id', 'store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute_label', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute_label', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Attribute Label');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_type'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Type Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => false,
    ], 'Code')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Label')
    ->addColumn('is_system', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Is System')
    ->addColumn('theme', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => true,
    ], 'Theme')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Store Id')
    ->addIndex(
        $installer->getIdxName(
            'eav/form_type',
            ['code', 'theme', 'store_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['code', 'theme', 'store_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('eav/form_type', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/form_type', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Form Type');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_type_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_type_entity'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Type Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Entity Type Id')
    ->addIndex(
        $installer->getIdxName('eav/form_type_entity', ['entity_type_id']),
        ['entity_type_id'],
    )
    ->addForeignKey(
        $installer->getFkName(
            'eav/form_type_entity',
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
    ->addForeignKey(
        $installer->getFkName('eav/form_type_entity', 'type_id', 'eav/form_type', 'type_id'),
        'type_id',
        $installer->getTable('eav/form_type'),
        'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Form Type Entity');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_fieldset'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_fieldset'))
    ->addColumn('fieldset_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Fieldset Id')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Type Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable'  => false,
    ], 'Code')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/form_fieldset',
            ['type_id', 'code'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['type_id', 'code'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('eav/form_fieldset', ['type_id']),
        ['type_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/form_fieldset', 'type_id', 'eav/form_type', 'type_id'),
        'type_id',
        $installer->getTable('eav/form_type'),
        'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Form Fieldset');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_fieldset_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_fieldset_label'))
    ->addColumn('fieldset_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Fieldset Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Store Id')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => false,
    ], 'Label')
    ->addIndex(
        $installer->getIdxName('eav/form_fieldset_label', ['fieldset_id']),
        ['fieldset_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/form_fieldset_label', ['store_id']),
        ['store_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/form_fieldset_label', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
        'fieldset_id',
        $installer->getTable('eav/form_fieldset'),
        'fieldset_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('eav/form_fieldset_label', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $installer->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Form Fieldset Label');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_element'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_element'))
    ->addColumn('element_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Element Id')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Type Id')
    ->addColumn('fieldset_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
    ], 'Fieldset Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
    ], 'Attribute Id')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/form_element',
            ['type_id', 'attribute_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['type_id', 'attribute_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('eav/form_element', ['type_id']),
        ['type_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/form_element', ['fieldset_id']),
        ['fieldset_id'],
    )
    ->addIndex(
        $installer->getIdxName('eav/form_element', ['attribute_id']),
        ['attribute_id'],
    )
    ->addForeignKey(
        $installer->getFkName('eav/form_element', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('eav/form_element', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
        'fieldset_id',
        $installer->getTable('eav/form_fieldset'),
        'fieldset_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->addForeignKey(
        $installer->getFkName('eav/form_element', 'type_id', 'eav/form_type', 'type_id'),
        'type_id',
        $installer->getTable('eav/form_type'),
        'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Form Element');
$installer->getConnection()->createTable($table);

$installer->endSetup();
