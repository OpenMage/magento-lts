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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute'),
    'FK_EAV_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_group'),
    'FK_EAV_ATTRIBUTE_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_label'),
    'FK_ATTRIBUTE_LABEL_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_label'),
    'FK_ATTRIBUTE_LABEL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_option'),
    'FK_ATTRIBUTE_OPTION_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/attribute_set'),
    'FK_EAV_ATTRIBUTE_SET'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity'),
    'FK_EAV_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity'),
    'FK_EAV_ENTITY_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIBUTE_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIBUTE_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIVUTE_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_element'),
    'FK_EAV_FORM_ELEMENT_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_element'),
    'FK_EAV_FORM_ELEMENT_FORM_FIELDSET'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_element'),
    'FK_EAV_FORM_ELEMENT_FORM_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_fieldset_label'),
    'FK_EAV_FORM_FIELDSET_LABEL_FORM_FIELDSET'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_fieldset_label'),
    'FK_EAV_FORM_FIELDSET_LABEL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_type'),
    'FK_EAV_FORM_TYPE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_type_entity'),
    'FK_EAV_FORM_TYPE_ENTITY_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_type_entity'),
    'FK_EAV_FORM_TYPE_ENTITY_FORM_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('eav/form_fieldset'),
    'FK_EAV_FORM_FIELDSET_FORM_TYPE'
);


$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'FK_EAV_ENTITY_DATETIME_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'FK_EAV_ENTITY_DATETIME_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'FK_EAV_ENTITY_DATETIME_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'FK_EAV_ENTITY_DECIMAL_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'FK_EAV_ENTITY_DECIMAL_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'FK_EAV_ENTITY_DECIMAL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'FK_EAV_ENTITY_INT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'FK_EAV_ENTITY_INT_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'FK_EAV_ENTITY_INT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'FK_EAV_ENTITY_TEXT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'FK_EAV_ENTITY_TEXT_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'FK_EAV_ENTITY_TEXT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'FK_EAV_ENTITY_VARCHAR_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'FK_EAV_ENTITY_VARCHAR_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'FK_EAV_ENTITY_VARCHAR_STORE'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute'),
    'ENTITY_TYPE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_group'),
    'ATTRIBUTE_SET_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_group'),
    'ATTRIBUTE_SET_ID_2'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_label'),
    'IDX_ATTRIBUTE_LABEL_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_label'),
    'IDX_ATTRIBUTE_LABEL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_label'),
    'IDX_ATTRIBUTE_LABEL_ATTRIBUTE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_option'),
    'FK_ATTRIBUTE_OPTION_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_OPTION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_option_value'),
    'FK_ATTRIBUTE_OPTION_VALUE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_set'),
    'ENTITY_TYPE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute_set'),
    'ENTITY_TYPE_ID_2'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity'),
    'FK_ENTITY_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity'),
    'FK_ENTITY_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'ATTRIBUTE_SET_ID_2'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'ATTRIBUTE_GROUP_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'ATTRIBUTE_SET_ID_3'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_attribute'),
    'FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_store'),
    'FK_EAV_ENTITY_STORE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/entity_type'),
    'ENTITY_NAME'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'UNQ_FORM_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'IDX_FORM_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'IDX_FORM_FIELDSET'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_element'),
    'IDX_FORM_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset'),
    'UNQ_FORM_FIELDSET_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset'),
    'IDX_FORM_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset_label'),
    'IDX_FORM_FIELDSET'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_fieldset_label'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_type'),
    'UNQ_FORM_TYPE_CODE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_type'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/form_type_entity'),
    'IDX_EAV_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'UNQ_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'FK_ATTRIBUTE_DATETIME_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'FK_ATTRIBUTE_DATETIME_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'FK_ATTRIBUTE_DATETIME_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'FK_ATTRIBUTE_DATETIME_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'VALUE_BY_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'VALUE_BY_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'UNQ_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'FK_ATTRIBUTE_DECIMAL_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'FK_ATTRIBUTE_DECIMAL_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'FK_ATTRIBUTE_DECIMAL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'FK_ATTRIBUTE_DECIMAL_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'VALUE_BY_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'VALUE_BY_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'UNQ_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'FK_ATTRIBUTE_INT_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'FK_ATTRIBUTE_INT_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'FK_ATTRIBUTE_INT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'FK_ATTRIBUTE_INT_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'VALUE_BY_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'VALUE_BY_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'UNQ_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'FK_ATTRIBUTE_TEXT_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'FK_ATTRIBUTE_TEXT_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'FK_ATTRIBUTE_TEXT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'FK_ATTRIBUTE_TEXT_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'UNQ_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'FK_ATTRIBUTE_VARCHAR_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'FK_ATTRIBUTE_VARCHAR_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'FK_ATTRIBUTE_VARCHAR_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'FK_ATTRIBUTE_VARCHAR_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'VALUE_BY_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'VALUE_BY_ENTITY_TYPE'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('eav/entity') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id'
            ),
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set Id'
            ),
            'increment_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Increment Id'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Created At'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated At'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Defines Is Entity Active'
            )
        ),
        'comment' => 'Eav Entity'
    ),
    $installer->getTable('eav/entity_type') => array(
        'columns' => array(
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Type Id'
            ),
            'entity_type_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Entity Type Code'
            ),
            'entity_model' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Entity Model'
            ),
            'attribute_model' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Attribute Model'
            ),
            'entity_table' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Entity Table'
            ),
            'value_table_prefix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value Table Prefix'
            ),
            'entity_id_field' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Entity Id Field'
            ),
            'is_data_sharing' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Defines Is Data Sharing'
            ),
            'data_sharing_key' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 100,
                'default'   => 'default',
                'comment'   => 'Data Sharing Key'
            ),
            'default_attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Default Attribute Set Id'
            ),
            'increment_model' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Increment Model'
            ),
            'increment_per_store' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Increment Per Store'
            ),
            'increment_pad_length' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '8',
                'comment'   => 'Increment Pad Length'
            ),
            'increment_pad_char' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 1,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Increment Pad Char'
            ),
            'additional_attribute_table' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Additional Attribute Table'
            ),
            'entity_attribute_collection' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => true,
                'default'   => '',
                'comment'   => 'Entity Attribute Collection'
            )
        ),
        'comment' => 'Eav Entity Type'
    ),
    $installer->getTable('eav/entity_store') => array(
        'columns' => array(
            'entity_store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Store Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'increment_prefix' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'comment'   => 'Increment Prefix'
            ),
            'increment_last_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Last Incremented Id'
            )
        ),
        'comment' => 'Eav Entity Store'
    ),
    $installer->getTable('eav/entity_attribute') => array(
        'columns' => array(
            'entity_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity Attribute Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id'
            ),
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set Id'
            ),
            'attribute_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Group Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Eav Entity Attributes'
    ),
    $installer->getTable('eav/attribute') => array(
        'columns' => array(
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id'
            ),
            'attribute_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Code'
            ),
            'attribute_model' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Attribute Model'
            ),
            'backend_model' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Backend Model'
            ),
            'backend_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 8,
                'nullable'  => false,
                'default'   => 'static',
                'comment'   => 'Backend Type'
            ),
            'backend_table' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Backend Table'
            ),
            'frontend_model' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Model'
            ),
            'frontend_input' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'Frontend Input'
            ),
            'frontend_label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Label'
            ),
            'frontend_class' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Class'
            ),
            'source_model' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Source Model'
            ),
            'is_required' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Defines Is Required'
            ),
            'is_user_defined' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Defines Is User Defined'
            ),
            'default_value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Default Value'
            ),
            'is_unique' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Defines Is Unique'
            ),
            'note' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Note'
            )
        ),
        'comment' => 'Eav Attribute'
    ),
    $installer->getTable('eav/attribute_set') => array(
        'columns' => array(
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Set Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type Id'
            ),
            'attribute_set_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Set Name'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Eav Attribute Set'
    ),
    $installer->getTable('eav/attribute_group') => array(
        'columns' => array(
            'attribute_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Group Id'
            ),
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set Id'
            ),
            'attribute_group_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Group Name'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            ),
            'default_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Default Id'
            )
        ),
        'comment' => 'Eav Attribute Group'
    ),
    $installer->getTable('eav/attribute_option') => array(
        'columns' => array(
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Eav Attribute Option'
    ),
    $installer->getTable('eav/attribute_option_value') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ),
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Eav Attribute Option Value'
    ),
    $installer->getTable('eav/attribute_label') => array(
        'columns' => array(
            'attribute_label_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Attribute Label Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Eav Attribute Label'
    ),
    $installer->getTable('eav/form_type') => array(
        'columns' => array(
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Type Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Code'
            ),
            'label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Label'
            ),
            'is_system' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is System'
            ),
            'theme' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'Theme'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store Id'
            )
        ),
        'comment' => 'Eav Form Type'
    ),
    $installer->getTable('eav/form_type_entity') => array(
        'columns' => array(
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Type Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity Type Id'
            )
        ),
        'comment' => 'Eav Form Type Entity'
    ),
    $installer->getTable('eav/form_fieldset') => array(
        'columns' => array(
            'fieldset_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Fieldset Id'
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Type Id'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'comment'   => 'Code'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Eav Form Fieldset'
    ),
    $installer->getTable('eav/form_fieldset_label') => array(
        'columns' => array(
            'fieldset_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Fieldset Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Label'
            )
        ),
        'comment' => 'Eav Form Fieldset Label'
    ),
    $installer->getTable('eav/form_element') => array(
        'columns' => array(
            'element_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Element Id'
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Type Id'
            ),
            'fieldset_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Fieldset Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Id'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Eav Form Element'
    )
    ,
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'nullable'  => false,
                'default' => '0000-00-00 00:00:00',
                'comment'   => 'Attribute Value'
            )
        ),
        'comment' => 'Eav Entity Value Prefix'
    ),
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Attribute Value'
            )
        ),
        'comment' => 'Eav Entity Value Prefix'
    ),
    $installer->getTable(array('eav/entity_value_prefix', 'int')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Attribute Value'
            )
        ),
        'comment' => 'Eav Entity Value Prefix'
    ),
    $installer->getTable(array('eav/entity_value_prefix', 'text')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'nullable'  => false,
                'comment'   => 'Attribute Value'
            )
        ),
        'comment' => 'Eav Entity Value Prefix'
    ),
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value Id'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type Id'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store Id'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Id'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Attribute Value'
            )
        ),
        'comment' => 'Eav Entity Value Prefix'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute'),
    $installer->getIdxName(
        'eav/attribute',
        array('entity_type_id', 'attribute_code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'attribute_code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute'),
    $installer->getIdxName('eav/attribute', array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_group'),
    $installer->getIdxName(
        'eav/attribute_group',
        array('attribute_set_id', 'attribute_group_name'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('attribute_set_id', 'attribute_group_name'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_group'),
    $installer->getIdxName('eav/attribute_group', array('attribute_set_id', 'sort_order')),
    array('attribute_set_id', 'sort_order')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_label'),
    $installer->getIdxName('eav/attribute_label', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_label'),
    $installer->getIdxName('eav/attribute_label', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_label'),
    $installer->getIdxName('eav/attribute_label', array('attribute_id', 'store_id')),
    array('attribute_id', 'store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_option'),
    $installer->getIdxName('eav/attribute_option', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_option_value'),
    $installer->getIdxName('eav/attribute_option_value', array('option_id')),
    array('option_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_option_value'),
    $installer->getIdxName('eav/attribute_option_value', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_set'),
    $installer->getIdxName(
        'eav/attribute_set',
        array('entity_type_id', 'attribute_set_name'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'attribute_set_name'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/attribute_set'),
    $installer->getIdxName('eav/attribute_set', array('entity_type_id', 'sort_order')),
    array('entity_type_id', 'sort_order')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity'),
    $installer->getIdxName('eav/entity', array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity'),
    $installer->getIdxName('eav/entity', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName(
        'eav/entity_attribute',
        array('attribute_set_id', 'attribute_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('attribute_set_id', 'attribute_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName(
        'eav/entity_attribute',
        array('attribute_group_id', 'attribute_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('attribute_group_id', 'attribute_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName('eav/entity_attribute', array('attribute_set_id', 'sort_order')),
    array('attribute_set_id', 'sort_order')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_attribute'),
    $installer->getIdxName('eav/entity_attribute', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_store'),
    $installer->getIdxName('eav/entity_store', array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_store'),
    $installer->getIdxName('eav/entity_store', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/entity_type'),
    $installer->getIdxName('eav/entity_type', array('entity_type_code')),
    array('entity_type_code')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName(
        'eav/form_element',
        array('type_id', 'attribute_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('type_id', 'attribute_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName('eav/form_element', array('type_id')),
    array('type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName('eav/form_element', array('fieldset_id')),
    array('fieldset_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_element'),
    $installer->getIdxName('eav/form_element', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset'),
    $installer->getIdxName(
        'eav/form_fieldset',
        array('type_id', 'code'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('type_id', 'code'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset'),
    $installer->getIdxName('eav/form_fieldset', array('type_id')),
    array('type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset_label'),
    $installer->getIdxName('eav/form_fieldset_label', array('fieldset_id')),
    array('fieldset_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_fieldset_label'),
    $installer->getIdxName('eav/form_fieldset_label', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_type'),
    $installer->getIdxName(
        'eav/form_type',
        array('code', 'theme', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('code', 'theme', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_type'),
    $installer->getIdxName('eav/form_type', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('eav/form_type_entity'),
    $installer->getIdxName('eav/form_type_entity', array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    $installer->getIdxName(
        array('eav/entity_value_prefix', 'datetime'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('attribute_id', 'value')),
    array('attribute_id', 'value')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'datetime'), array('entity_type_id', 'value')),
    array('entity_type_id', 'value')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    $installer->getIdxName(
        array('eav/entity_value_prefix', 'decimal'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('attribute_id', 'value')),
    array('attribute_id', 'value')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'decimal'), array('entity_type_id', 'value')),
    array('entity_type_id', 'value')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    $installer->getIdxName(
        array('eav/entity_value_prefix', 'int'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('attribute_id', 'value')),
    array('attribute_id', 'value')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'int'), array('entity_type_id', 'value')),
    array('entity_type_id', 'value')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    $installer->getIdxName(
        array('eav/entity_value_prefix', 'text'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'text'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    $installer->getIdxName(
        array('eav/entity_value_prefix', 'varchar'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('attribute_id', 'value')),
    array('attribute_id', 'value')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    $installer->getIdxName(array('eav/entity_value_prefix', 'varchar'), array('entity_type_id', 'value')),
    array('entity_type_id', 'value')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/attribute'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_group', 'attribute_set_id', 'eav/attribute_set', 'attribute_set_id'),
    $installer->getTable('eav/attribute_group'),
    'attribute_set_id',
    $installer->getTable('eav/attribute_set'),
    'attribute_set_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_label', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/attribute_label'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/attribute_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_option', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/attribute_option'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_option_value', 'option_id', 'eav/attribute_option', 'option_id'),
    $installer->getTable('eav/attribute_option_value'),
    'option_id',
    $installer->getTable('eav/attribute_option'),
    'option_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_option_value', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/attribute_option_value'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/attribute_set', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/attribute_set'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/entity'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/entity'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/entity_attribute'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);


$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_attribute', 'attribute_group_id', 'eav/attribute_group', 'attribute_group_id'),
    $installer->getTable('eav/entity_attribute'),
    'attribute_group_id',
    $installer->getTable('eav/attribute_group'),
    'attribute_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_store', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/entity_store'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/entity_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/entity_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_element', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('eav/form_element'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_element', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
    $installer->getTable('eav/form_element'),
    'fieldset_id',
    $installer->getTable('eav/form_fieldset'),
    'fieldset_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_element', 'type_id', 'eav/form_type', 'type_id'),
    $installer->getTable('eav/form_element'),
    'type_id',
    $installer->getTable('eav/form_type'),
    'type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_fieldset', 'type_id', 'eav/form_type', 'type_id'),
    $installer->getTable('eav/form_fieldset'),
    'type_id',
    $installer->getTable('eav/form_type'),
    'type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_fieldset_label', 'fieldset_id', 'eav/form_fieldset', 'fieldset_id'),
    $installer->getTable('eav/form_fieldset_label'),
    'fieldset_id',
    $installer->getTable('eav/form_fieldset'),
    'fieldset_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_fieldset_label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/form_fieldset_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_type', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('eav/form_type'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_type_entity', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable('eav/form_type_entity'),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('eav/form_type_entity', 'type_id', 'eav/form_type', 'type_id'),
    $installer->getTable('eav/form_type_entity'),
    'type_id',
    $installer->getTable('eav/form_type'),
    'type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'datetime'), 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'datetime'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'datetime'), 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'datetime')),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'decimal'), 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'decimal'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'decimal'), 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'decimal')),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'int'), 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'int'), 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'int'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'int')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'text'), 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'text'), 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'text'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'text')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'varchar'), 'entity_id', 'eav/entity', 'entity_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'entity_id',
    $installer->getTable('eav/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'varchar'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('eav/entity_value_prefix', 'varchar'), 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
    $installer->getTable(array('eav/entity_value_prefix', 'varchar')),
    'entity_type_id',
    $installer->getTable('eav/entity_type'),
    'entity_type_id'
);

$installer->endSetup();
