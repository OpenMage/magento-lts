<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'shipping/tablerate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('shipping/tablerate'))
    ->addColumn('pk', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Primary key')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Website Id')
    ->addColumn('dest_country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 4, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Destination coutry ISO/2 or ISO/3 code')
    ->addColumn('dest_region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => false,
        'default'   => '0',
    ], 'Destination Region Id')
    ->addColumn('dest_zip', Varien_Db_Ddl_Table::TYPE_TEXT, 10, [
        'nullable'  => false,
        'default'   => '*',
    ], 'Destination Post Code (Zip)')
    ->addColumn('condition_name', Varien_Db_Ddl_Table::TYPE_TEXT, 20, [
        'nullable'  => false,
    ], 'Rate Condition name')
    ->addColumn('condition_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Rate condition value')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Price')
    ->addColumn('cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable'  => false,
        'default'   => '0.0000',
    ], 'Cost')
    ->addIndex(
        $installer->getIdxName('shipping/tablerate', ['website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        ['website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE],
    )
    ->setComment('Shipping Tablerate');
$installer->getConnection()->createTable($table);

$installer->endSetup();
