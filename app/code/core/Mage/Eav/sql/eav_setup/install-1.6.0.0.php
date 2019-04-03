<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

/**
 * Create table 'eav/entity_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity_type'))
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Type Id')
    ->addColumn('entity_type_code', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        ), 'Entity Type Code')
    ->addColumn('entity_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Entity Model')
    ->addColumn('attribute_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Attribute Model')
    ->addColumn('entity_table', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Entity Table')
    ->addColumn('value_table_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value Table Prefix')
    ->addColumn('entity_id_field', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Entity Id Field')
    ->addColumn('is_data_sharing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Defines Is Data Sharing')
    ->addColumn('data_sharing_key', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'default'   => 'default',
        ), 'Data Sharing Key')
    ->addColumn('default_attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Default Attribute Set Id')
    ->addColumn('increment_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => '',
        ), 'Increment Model')
    ->addColumn('increment_per_store', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Increment Per Store')
    ->addColumn('increment_pad_length', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '8',
        ), 'Increment Pad Length')
    ->addColumn('increment_pad_char', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Increment Pad Char')
    ->addColumn('additional_attribute_table', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => '',
        ), 'Additional Attribute Table')
    ->addColumn('entity_attribute_collection', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Entity Attribute Collection')
    ->addIndex($installer->getIdxName('eav/entity_type', array('entity_type_code')),
        array('entity_type_code'))
    ->setComment('Eav Entity Type');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Set Id')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Increment Id')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Parent Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated At')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Defines Is Entity Active')
    ->addIndex($installer->getIdxName('eav/entity', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('eav/entity', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('eav/entity', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('eav/entity', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'datetime')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('eav/entity_value_prefix', 'datetime')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
        'default' => $installer->getConnection()->getSuggestedZeroDate()
        ), 'Attribute Value')
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('attribute_id', 'value')),
        array('attribute_id', 'value'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('entity_type_id', 'value')),
        array('entity_type_id', 'value'))
    ->addIndex(
        $installer->getIdxName(
            array('eav/entity_value_prefix', 'datetime'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'datetime'),
            'entity_id',
            'eav/entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('eav/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'datetime'),
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'datetime'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'decimal')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('eav/entity_value_prefix', 'decimal')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Attribute Value')
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('attribute_id', 'value')),
        array('attribute_id', 'value'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('entity_type_id', 'value')),
        array('entity_type_id', 'value'))
    ->addIndex(
        $installer->getIdxName(
            array('eav/entity_value_prefix', 'decimal'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'decimal'),
            'entity_id',
            'eav/entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('eav/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'decimal'),
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'decimal'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'int')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('eav/entity_value_prefix', 'int')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Value')
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('attribute_id', 'value')),
        array('attribute_id', 'value'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('entity_type_id', 'value')),
        array('entity_type_id', 'value'))
    ->addIndex(
        $installer->getIdxName(
            array('eav/entity_value_prefix', 'int'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'int'),
            'entity_id',
            'eav/entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('eav/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'int'),
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'int'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'text')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('eav/entity_value_prefix', 'text')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Attribute Value')
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('entity_id')),
        array('entity_id'))
    ->addIndex(
        $installer->getIdxName(
            array('eav/entity_value_prefix', 'text'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'text'),
            'entity_id',
            'eav/entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('eav/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'text'),
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'text'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table array('eav/entity_value_prefix', 'varchar')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('eav/entity_value_prefix', 'varchar')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Attribute Value')
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('attribute_id', 'value')),
        array('attribute_id', 'value'))
    ->addIndex($installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('entity_type_id', 'value')),
        array('entity_type_id', 'value'))
    ->addIndex(
        $installer->getIdxName(
            array('eav/entity_value_prefix', 'varchar'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'varchar'),
            'entity_id',
            'eav/entity',
            'entity_id'
        ),
        'entity_id', $installer->getTable('eav/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName(
            array('eav/entity_value_prefix', 'varchar'),
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('eav/entity_value_prefix', 'varchar'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity Value Prefix');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Attribute Code')
    ->addColumn('attribute_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Attribute Model')
    ->addColumn('backend_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Backend Model')
    ->addColumn('backend_type', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(
        'nullable'  => false,
        'default'   => 'static',
        ), 'Backend Type')
    ->addColumn('backend_table', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Backend Table')
    ->addColumn('frontend_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Frontend Model')
    ->addColumn('frontend_input', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Frontend Input')
    ->addColumn('frontend_label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Frontend Label')
    ->addColumn('frontend_class', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Frontend Class')
    ->addColumn('source_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Source Model')
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Defines Is Required')
    ->addColumn('is_user_defined', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Defines Is User Defined')
    ->addColumn('default_value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Default Value')
    ->addColumn('is_unique', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Defines Is Unique')
    ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Note')
    ->addIndex(
        $installer->getIdxName(
            'eav/attribute',
            array('entity_type_id', 'attribute_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'attribute_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('eav/attribute', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('eav/attribute', array('entity_type_id')),
        array('entity_type_id'))
    ->addForeignKey($installer->getFkName('eav/attribute', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/entity_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity_store'))
    ->addColumn('entity_store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Store Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('increment_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        'nullable'  => true,
        ), 'Increment Prefix')
    ->addColumn('increment_last_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => true,
        ), 'Last Incremented Id')
    ->addIndex($installer->getIdxName('eav/entity_store', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('eav/entity_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('eav/entity_store', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('eav/entity_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity Store');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_set'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_set'))
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Set Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_set_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Attribute Set Name')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/attribute_set',
            array('entity_type_id', 'attribute_set_name'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'attribute_set_name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('eav/attribute_set', array('entity_type_id', 'sort_order')),
        array('entity_type_id', 'sort_order'))
    ->addForeignKey($installer->getFkName('eav/attribute_set', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute Set');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_group'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_group'))
    ->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Group Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Set Id')
    ->addColumn('attribute_group_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Attribute Group Name')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addColumn('default_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Default Id')
    ->addIndex(
        $installer->getIdxName(
            'eav/attribute_group',
            array('attribute_set_id', 'attribute_group_name'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('attribute_set_id', 'attribute_group_name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('eav/attribute_group', array('attribute_set_id', 'sort_order')),
        array('attribute_set_id', 'sort_order'))
    ->addForeignKey(
        $installer->getFkName(
            'eav/attribute_group',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id', $installer->getTable('eav/attribute_set'), 'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute Group');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/entity_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/entity_attribute'))
    ->addColumn('entity_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Attribute Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Set Id')
    ->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Group Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/entity_attribute',
            array('attribute_set_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('attribute_set_id', 'attribute_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            'eav/entity_attribute',
            array('attribute_group_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('attribute_group_id', 'attribute_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('eav/entity_attribute', array('attribute_set_id', 'sort_order')),
        array('attribute_set_id', 'sort_order'))
    ->addIndex($installer->getIdxName('eav/entity_attribute', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey($installer->getFkName('eav/entity_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'eav/entity_attribute',
            'attribute_group_id',
            'eav/attribute_group',
            'attribute_group_id'
        ),
        'attribute_group_id', $installer->getTable('eav/attribute_group'), 'attribute_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Entity Attributes');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('eav/attribute_option', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey($installer->getFkName('eav/attribute_option', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute Option');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_option_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_option_value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Option Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Value')
    ->addIndex($installer->getIdxName('eav/attribute_option_value', array('option_id')),
        array('option_id'))
    ->addIndex($installer->getIdxName('eav/attribute_option_value', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName('eav/attribute_option_value', 'option_id', 'eav/attribute_option', 'option_id'),
        'option_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('eav/attribute_option_value', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute Option Value');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/attribute_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_label'))
    ->addColumn('attribute_label_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Label Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Value')
    ->addIndex($installer->getIdxName('eav/attribute_label', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('eav/attribute_label', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('eav/attribute_label', array('attribute_id', 'store_id')),
        array('attribute_id', 'store_id'))
    ->addForeignKey($installer->getFkName('eav/attribute_label', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('eav/attribute_label', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Attribute Label');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_type'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Type Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        ), 'Code')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Label')
    ->addColumn('is_system', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is System')
    ->addColumn('theme', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => true,
        ), 'Theme')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store Id')
    ->addIndex(
        $installer->getIdxName(
            'eav/form_type',
            array('code', 'theme', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('code', 'theme', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('eav/form_type', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('eav/form_type', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Form Type');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_type_entity'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_type_entity'))
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Type Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Type Id')
    ->addIndex($installer->getIdxName('eav/form_type_entity', array('entity_type_id')),
        array('entity_type_id'))
    ->addForeignKey(
        $installer->getFkName(
            'eav/form_type_entity',
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('eav/form_type_entity', 'type_id', 'eav/form_type', 'type_id'),
        'type_id', $installer->getTable('eav/form_type'), 'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Form Type Entity');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_fieldset'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_fieldset'))
    ->addColumn('fieldset_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Fieldset Id')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Type Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        ), 'Code')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/form_fieldset',
            array('type_id', 'code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('type_id', 'code'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('eav/form_fieldset', array('type_id')),
        array('type_id'))
    ->addForeignKey($installer->getFkName('eav/form_fieldset', 'type_id', 'eav/form_type', 'type_id'),
        'type_id', $installer->getTable('eav/form_type'), 'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Form Fieldset');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_fieldset_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_fieldset_label'))
    ->addColumn('fieldset_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Fieldset Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store Id')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Label')
    ->addIndex($installer->getIdxName('eav/form_fieldset_label', array('fieldset_id')),
        array('fieldset_id'))
    ->addIndex($installer->getIdxName('eav/form_fieldset_label', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName('eav/form_fieldset_label', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
        'fieldset_id', $installer->getTable('eav/form_fieldset'), 'fieldset_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('eav/form_fieldset_label', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Form Fieldset Label');
$installer->getConnection()->createTable($table);

/**
 * Create table 'eav/form_element'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/form_element'))
    ->addColumn('element_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Element Id')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Type Id')
    ->addColumn('fieldset_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Fieldset Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Attribute Id')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex(
        $installer->getIdxName(
            'eav/form_element',
            array('type_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('type_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('eav/form_element', array('type_id')),
        array('type_id'))
    ->addIndex($installer->getIdxName('eav/form_element', array('fieldset_id')),
        array('fieldset_id'))
    ->addIndex($installer->getIdxName('eav/form_element', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey($installer->getFkName('eav/form_element', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('eav/form_element', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
        'fieldset_id', $installer->getTable('eav/form_fieldset'), 'fieldset_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('eav/form_element', 'type_id', 'eav/form_type', 'type_id'),
        'type_id', $installer->getTable('eav/form_type'), 'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Eav Form Element');
$installer->getConnection()->createTable($table);

$installer->endSetup();
