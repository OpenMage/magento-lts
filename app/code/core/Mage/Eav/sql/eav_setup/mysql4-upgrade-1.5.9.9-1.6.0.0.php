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
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute'),
    'FK_EAV_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_group'),
    'FK_EAV_ATTRIBUTE_GROUP',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_label'),
    'FK_ATTRIBUTE_LABEL_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_label'),
    'FK_ATTRIBUTE_LABEL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_option'),
    'FK_ATTRIBUTE_OPTION_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_OPTION',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_set'),
    'FK_EAV_ATTRIBUTE_SET',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity'),
    'FK_EAV_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity'),
    'FK_EAV_ENTITY_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIBUTE_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIBUTE_GROUP',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIVUTE_GROUP',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_element'),
    'FK_EAV_FORM_ELEMENT_ATTRIBUTE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_element'),
    'FK_EAV_FORM_ELEMENT_FORM_FIELDSET',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_element'),
    'FK_EAV_FORM_ELEMENT_FORM_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_fieldset_label'),
    'FK_EAV_FORM_FIELDSET_LABEL_FORM_FIELDSET',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_fieldset_label'),
    'FK_EAV_FORM_FIELDSET_LABEL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_type'),
    'FK_EAV_FORM_TYPE_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_type_entity'),
    'FK_EAV_FORM_TYPE_ENTITY_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_type_entity'),
    'FK_EAV_FORM_TYPE_ENTITY_FORM_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_fieldset'),
    'FK_EAV_FORM_FIELDSET_FORM_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'FK_EAV_ENTITY_DATETIME_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'FK_EAV_ENTITY_DATETIME_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'FK_EAV_ENTITY_DATETIME_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'FK_EAV_ENTITY_DECIMAL_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'FK_EAV_ENTITY_DECIMAL_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'FK_EAV_ENTITY_DECIMAL_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'FK_EAV_ENTITY_INT_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'FK_EAV_ENTITY_INT_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'FK_EAV_ENTITY_INT_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'FK_EAV_ENTITY_TEXT_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'FK_EAV_ENTITY_TEXT_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'FK_EAV_ENTITY_TEXT_STORE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'FK_EAV_ENTITY_VARCHAR_ENTITY',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'FK_EAV_ENTITY_VARCHAR_ENTITY_TYPE',
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'FK_EAV_ENTITY_VARCHAR_STORE',
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute'),
    'ENTITY_TYPE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_group'),
    'ATTRIBUTE_SET_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_group'),
    'ATTRIBUTE_SET_ID_2',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_label'),
    'IDX_ATTRIBUTE_LABEL_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_label'),
    'IDX_ATTRIBUTE_LABEL_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_label'),
    'IDX_ATTRIBUTE_LABEL_ATTRIBUTE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_option'),
    'FK_ATTRIBUTE_OPTION_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_OPTION',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_set'),
    'ENTITY_TYPE_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_set'),
    'ENTITY_TYPE_ID_2',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity'),
    'FK_ENTITY_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity'),
    'FK_ENTITY_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'ATTRIBUTE_SET_ID_2',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'ATTRIBUTE_GROUP_ID',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'ATTRIBUTE_SET_ID_3',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_type'),
    'ENTITY_NAME',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'UNQ_FORM_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'IDX_FORM_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'IDX_FORM_FIELDSET',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'IDX_FORM_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset'),
    'UNQ_FORM_FIELDSET_CODE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset'),
    'IDX_FORM_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset_label'),
    'IDX_FORM_FIELDSET',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset_label'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_type'),
    'UNQ_FORM_TYPE_CODE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_type'),
    'IDX_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_type_entity'),
    'IDX_EAV_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'UNQ_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'FK_ATTRIBUTE_DATETIME_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'FK_ATTRIBUTE_DATETIME_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'FK_ATTRIBUTE_DATETIME_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'FK_ATTRIBUTE_DATETIME_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'VALUE_BY_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'VALUE_BY_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'UNQ_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'FK_ATTRIBUTE_DECIMAL_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'FK_ATTRIBUTE_DECIMAL_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'FK_ATTRIBUTE_DECIMAL_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'FK_ATTRIBUTE_DECIMAL_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'VALUE_BY_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'VALUE_BY_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'UNQ_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'FK_ATTRIBUTE_INT_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'FK_ATTRIBUTE_INT_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'FK_ATTRIBUTE_INT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'FK_ATTRIBUTE_INT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'VALUE_BY_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'VALUE_BY_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'UNQ_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'FK_ATTRIBUTE_TEXT_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'FK_ATTRIBUTE_TEXT_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'FK_ATTRIBUTE_TEXT_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'FK_ATTRIBUTE_TEXT_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'UNQ_ATTRIBUTE_VALUE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'FK_ATTRIBUTE_VARCHAR_ENTITY_TYPE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'FK_ATTRIBUTE_VARCHAR_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'FK_ATTRIBUTE_VARCHAR_STORE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'FK_ATTRIBUTE_VARCHAR_ENTITY',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'VALUE_BY_ATTRIBUTE',
);

$installer->getConnection()->dropIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'VALUE_BY_ENTITY_TYPE',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('eav/entity') => [
        'columns' => [
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id',
            ],
            'attribute_set_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set Id',
            ],
            'increment_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Increment Id',
            ],
            'parent_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'created_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At',
            ],
            'updated_at' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At',
            ],
            'is_active' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Defines Is Entity Active',
            ],
        ],
        'comment' => 'Eav Entity',
    ],
    $installer->getTable('eav/entity_type') => [
        'columns' => [
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Type Id',
            ],
            'entity_type_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Entity Type Code',
            ],
            'entity_model' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Entity Model',
            ],
            'attribute_model' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Attribute Model',
            ],
            'entity_table' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Entity Table',
            ],
            'value_table_prefix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value Table Prefix',
            ],
            'entity_id_field' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Entity Id Field',
            ],
            'is_data_sharing' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Defines Is Data Sharing',
            ],
            'data_sharing_key' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'default'   => 'default',
                'comment'   => 'Data Sharing Key',
            ],
            'default_attribute_set_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Default Attribute Set Id',
            ],
            'increment_model' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Increment Model',
            ],
            'increment_per_store' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Increment Per Store',
            ],
            'increment_pad_length' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '8',
                'comment'   => 'Increment Pad Length',
            ],
            'increment_pad_char' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Increment Pad Char',
            ],
            'additional_attribute_table' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Additional Attribute Table',
            ],
            'entity_attribute_collection' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Entity Attribute Collection',
            ],
        ],
        'comment' => 'Eav Entity Type',
    ],
    $installer->getTable('eav/entity_store') => [
        'columns' => [
            'entity_store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Store Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'increment_prefix' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Increment Prefix',
            ],
            'increment_last_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Last Incremented Id',
            ],
        ],
        'comment' => 'Eav Entity Store',
    ],
    $installer->getTable('eav/entity_attribute') => [
        'columns' => [
            'entity_attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Attribute Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id',
            ],
            'attribute_set_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set Id',
            ],
            'attribute_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Group Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Eav Entity Attributes',
    ],
    $installer->getTable('eav/attribute') => [
        'columns' => [
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id',
            ],
            'attribute_code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Code',
            ],
            'attribute_model' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Attribute Model',
            ],
            'backend_model' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Backend Model',
            ],
            'backend_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'default'   => 'static',
                'comment'   => 'Backend Type',
            ],
            'backend_table' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Backend Table',
            ],
            'frontend_model' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Model',
            ],
            'frontend_input' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Frontend Input',
            ],
            'frontend_label' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Label',
            ],
            'frontend_class' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Class',
            ],
            'source_model' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Source Model',
            ],
            'is_required' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Defines Is Required',
            ],
            'is_user_defined' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Defines Is User Defined',
            ],
            'default_value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Default Value',
            ],
            'is_unique' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Defines Is Unique',
            ],
            'note' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Note',
            ],
        ],
        'comment' => 'Eav Attribute',
    ],
    $installer->getTable('eav/attribute_set') => [
        'columns' => [
            'attribute_set_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Set Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id',
            ],
            'attribute_set_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Set Name',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Eav Attribute Set',
    ],
    $installer->getTable('eav/attribute_group') => [
        'columns' => [
            'attribute_group_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Group Id',
            ],
            'attribute_set_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set Id',
            ],
            'attribute_group_name' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Group Name',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
            'default_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Default Id',
            ],
        ],
        'comment' => 'Eav Attribute Group',
    ],
    $installer->getTable('eav/attribute_option') => [
        'columns' => [
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Eav Attribute Option',
    ],
    $installer->getTable('eav/attribute_option_value') => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id',
            ],
            'option_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Eav Attribute Option Value',
    ],
    $installer->getTable('eav/attribute_label') => [
        'columns' => [
            'attribute_label_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Label Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value',
            ],
        ],
        'comment' => 'Eav Attribute Label',
    ],
    $installer->getTable('eav/form_type') => [
        'columns' => [
            'type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Type Id',
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Code',
            ],
            'label' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Label',
            ],
            'is_system' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is System',
            ],
            'theme' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Theme',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
        ],
        'comment' => 'Eav Form Type',
    ],
    $installer->getTable('eav/form_type_entity') => [
        'columns' => [
            'type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Type Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity Type Id',
            ],
        ],
        'comment' => 'Eav Form Type Entity',
    ],
    $installer->getTable('eav/form_fieldset') => [
        'columns' => [
            'fieldset_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Fieldset Id',
            ],
            'type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Type Id',
            ],
            'code' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Code',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Eav Form Fieldset',
    ],
    $installer->getTable('eav/form_fieldset_label') => [
        'columns' => [
            'fieldset_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Fieldset Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id',
            ],
            'label' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Label',
            ],
        ],
        'comment' => 'Eav Form Fieldset Label',
    ],
    $installer->getTable('eav/form_element') => [
        'columns' => [
            'element_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Element Id',
            ],
            'type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Type Id',
            ],
            'fieldset_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Fieldset Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id',
            ],
            'sort_order' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order',
            ],
        ],
        'comment' => 'Eav Form Element',
    ]
    ,
    $installer->getTable(['eav/entity_value_prefix', 'datetime']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'nullable'  => false,
                'default' => '0000-00-00 00:00:00',
                'comment'   => 'Attribute Value',
            ],
        ],
        'comment' => 'Eav Entity Value Prefix',
    ],
    $installer->getTable(['eav/entity_value_prefix', 'decimal']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Attribute Value',
            ],
        ],
        'comment' => 'Eav Entity Value Prefix',
    ],
    $installer->getTable(['eav/entity_value_prefix', 'int']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Attribute Value',
            ],
        ],
        'comment' => 'Eav Entity Value Prefix',
    ],
    $installer->getTable(['eav/entity_value_prefix', 'text']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Attribute Value',
            ],
        ],
        'comment' => 'Eav Entity Value Prefix',
    ],
    $installer->getTable(['eav/entity_value_prefix', 'varchar']) => [
        'columns' => [
            'value_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id',
            ],
            'entity_type_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id',
            ],
            'attribute_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id',
            ],
            'entity_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id',
            ],
            'value' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Value',
            ],
        ],
        'comment' => 'Eav Entity Value Prefix',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute'),
    $installer->getIdxName(
        'eav/attribute',
        ['entity_type_id', 'attribute_code'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'attribute_code'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute'),
    $installer->getIdxName('eav/attribute', ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_group'),
    $installer->getIdxName(
        'eav/attribute_group',
        ['attribute_set_id', 'attribute_group_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['attribute_set_id', 'attribute_group_name'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_group'),
    $installer->getIdxName('eav/attribute_group', ['attribute_set_id', 'sort_order']),
    ['attribute_set_id', 'sort_order'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_label'),
    $installer->getIdxName('eav/attribute_label', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_label'),
    $installer->getIdxName('eav/attribute_label', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_label'),
    $installer->getIdxName('eav/attribute_label', ['attribute_id', 'store_id']),
    ['attribute_id', 'store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_option'),
    $installer->getIdxName('eav/attribute_option', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_option_value'),
    $installer->getIdxName('eav/attribute_option_value', ['option_id']),
    ['option_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_option_value'),
    $installer->getIdxName('eav/attribute_option_value', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_set'),
    $installer->getIdxName(
        'eav/attribute_set',
        ['entity_type_id', 'attribute_set_name'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_type_id', 'attribute_set_name'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_set'),
    $installer->getIdxName('eav/attribute_set', ['entity_type_id', 'sort_order']),
    ['entity_type_id', 'sort_order'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity'),
    $installer->getIdxName('eav/entity', ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity'),
    $installer->getIdxName('eav/entity', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName(
        'eav/entity_attribute',
        ['attribute_set_id', 'attribute_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['attribute_set_id', 'attribute_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName(
        'eav/entity_attribute',
        ['attribute_group_id', 'attribute_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['attribute_group_id', 'attribute_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName('eav/entity_attribute', ['attribute_set_id', 'sort_order']),
    ['attribute_set_id', 'sort_order'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName('eav/entity_attribute', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_store'),
    $installer->getIdxName('eav/entity_store', ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_store'),
    $installer->getIdxName('eav/entity_store', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_type'),
    $installer->getIdxName('eav/entity_type', ['entity_type_code']),
    ['entity_type_code'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName(
        'eav/form_element',
        ['type_id', 'attribute_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['type_id', 'attribute_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName('eav/form_element', ['type_id']),
    ['type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName('eav/form_element', ['fieldset_id']),
    ['fieldset_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName('eav/form_element', ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset'),
    $installer->getIdxName(
        'eav/form_fieldset',
        ['type_id', 'code'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['type_id', 'code'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset'),
    $installer->getIdxName('eav/form_fieldset', ['type_id']),
    ['type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset_label'),
    $installer->getIdxName('eav/form_fieldset_label', ['fieldset_id']),
    ['fieldset_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset_label'),
    $installer->getIdxName('eav/form_fieldset_label', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_type'),
    $installer->getIdxName(
        'eav/form_type',
        ['code', 'theme', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['code', 'theme', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_type'),
    $installer->getIdxName('eav/form_type', ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_type_entity'),
    $installer->getIdxName('eav/form_type_entity', ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    $installer->getIdxName(
        ['eav/entity_value_prefix', 'datetime'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['attribute_id', 'value']),
    ['attribute_id', 'value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    $installer->getIdxName(['eav/entity_value_prefix', 'datetime'], ['entity_type_id', 'value']),
    ['entity_type_id', 'value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    $installer->getIdxName(
        ['eav/entity_value_prefix', 'decimal'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['attribute_id', 'value']),
    ['attribute_id', 'value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    $installer->getIdxName(['eav/entity_value_prefix', 'decimal'], ['entity_type_id', 'value']),
    ['entity_type_id', 'value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    $installer->getIdxName(
        ['eav/entity_value_prefix', 'int'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['attribute_id', 'value']),
    ['attribute_id', 'value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    $installer->getIdxName(['eav/entity_value_prefix', 'int'], ['entity_type_id', 'value']),
    ['entity_type_id', 'value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    $installer->getIdxName(
        ['eav/entity_value_prefix', 'text'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    $installer->getIdxName(['eav/entity_value_prefix', 'text'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    $installer->getIdxName(
        ['eav/entity_value_prefix', 'varchar'],
        ['entity_id', 'attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['entity_id', 'attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['entity_type_id']),
    ['entity_type_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['attribute_id']),
    ['attribute_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['store_id']),
    ['store_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['entity_id']),
    ['entity_id'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['attribute_id', 'value']),
    ['attribute_id', 'value'],
);

$installer->getConnection()->addIndex(
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    $installer->getIdxName(['eav/entity_value_prefix', 'varchar'], ['entity_type_id', 'value']),
    ['entity_type_id', 'value'],
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/attribute'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_group', 'attribute_set_id', 'eav/attribute_set', 'attribute_set_id'),
    $installer->getTable('eav/attribute_group'),
    'attribute_set_id',
    $installer->getTable('eav/attribute_set'),
    'attribute_set_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_label', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/attribute_label'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/attribute_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_option', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/attribute_option'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_option_value', 'option_id', 'eav/attribute_option', 'option_id'),
    $installer->getTable('eav/attribute_option_value'),
    'option_id',
    $installer->getTable('eav/attribute_option'),
    'option_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_option_value', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/attribute_option_value'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_set', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/attribute_set'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/entity'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/entity'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/entity_attribute'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_attribute', 'attribute_group_id', 'eav/attribute_group', 'attribute_group_id'),
    $installer->getTable('eav/entity_attribute'),
    'attribute_group_id',
    $installer->getTable('eav/attribute_group'),
    'attribute_group_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_store', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/entity_store'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/entity_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_element', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/form_element'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_element', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
    $installer->getTable('eav/form_element'),
    'fieldset_id',
    $installer->getTable('eav/form_fieldset'),
    'fieldset_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_element', 'type_id', 'eav/form_type', 'type_id'),
    $installer->getTable('eav/form_element'),
    'type_id',
    $installer->getTable('eav/form_type'),
    'type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_fieldset', 'type_id', 'eav/form_type', 'type_id'),
    $installer->getTable('eav/form_fieldset'),
    'type_id',
    $installer->getTable('eav/form_type'),
    'type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_fieldset_label', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
    $installer->getTable('eav/form_fieldset_label'),
    'fieldset_id',
    $installer->getTable('eav/form_fieldset'),
    'fieldset_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_fieldset_label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/form_fieldset_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_type', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/form_type'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_type_entity', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/form_type_entity'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_type_entity', 'type_id', 'eav/form_type', 'type_id'),
    $installer->getTable('eav/form_type_entity'),
    'type_id',
    $installer->getTable('eav/form_type'),
    'type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'datetime'], 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'datetime'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'datetime'], 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(['eav/entity_value_prefix', 'datetime']),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'decimal'], 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'decimal'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'decimal'], 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(['eav/entity_value_prefix', 'decimal']),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'int'], 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'int'], 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'int'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['eav/entity_value_prefix', 'int']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'text'], 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'text'], 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'text'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['eav/entity_value_prefix', 'text']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'varchar'], 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'varchar'], 'store_id', 'core/store', 'store_id'),
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(['eav/entity_value_prefix', 'varchar'], 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(['eav/entity_value_prefix', 'varchar']),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id',
);

$installer->endSetup();
