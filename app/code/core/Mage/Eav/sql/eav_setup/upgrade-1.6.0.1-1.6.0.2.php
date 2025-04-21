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
 * Create table 'eav/attribute_option_swatch'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eav/attribute_option_swatch'))
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
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Value')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable'  => true,
        'default'   => null,
    ], 'Filename')
    ->addIndex(
        $installer->getIdxName('eav/attribute_option_value', ['option_id']),
        ['option_id'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->addForeignKey(
        $installer->getFkName('eav/attribute_option_swatch', 'option_id', 'eav/attribute_option', 'option_id'),
        'option_id',
        $installer->getTable('eav/attribute_option'),
        'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('Eav Attribute Option Swatch');
$installer->getConnection()->createTable($table);
